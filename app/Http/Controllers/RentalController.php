<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:rental-list')->only(['index', 'show']);
        $this->middleware('permission:rental-create')->only(['create', 'store']);
        $this->middleware('permission:rental-edit')->only(['edit', 'update']);
        $this->middleware('permission:rental-delete')->only(['destroy']);
        $this->middleware('permission:rental-return')->only(['returnCar']);
    }

    public function index()
    {
        $rentals = Rental::with(['car', 'customer'])->latest()->paginate(10);
        return view('rentals.index', compact('rentals'));
    }

    public function create()
    {
        $cars = Car::where('status', 'available')->get();
        $drivers = Driver::where('status', 'available')->get();
        $customers = Customer::all();

        return view('rentals.create', compact('cars', 'drivers', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'customer_id' => 'required|exists:customers,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'start_time' => 'required|date',
            'expected_end_time' => 'required|date|after:start_time',
            'price_per_day' => 'required|numeric|min:0',
            'driver_price_per_day' => 'required_with:driver_id|nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $rental = new Rental($validated);
            $rental->status = 'active';
            $rental->expected_amount = $rental->calculateExpectedAmount();
            $rental->save();

            Car::where('id', $rental->car_id)->update(['status' => 'rented']);

            if ($rental->driver_id) {
                Driver::where('id', $rental->driver_id)->update(['status' => 'assigned']);
            }

            if ($rental->paid_amount > 0) {
                Account::create([
                    'type' => 'income',
                    'amount' => $rental->paid_amount,
                    'description' => __('messages.rental_payment') . ' - ' . __('messages.rental_id') . ': ' . $rental->id,
                    'date' => now()
                ]);
            }

            DB::commit();

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => __('messages.rental_created'), 'redirect' => route('rentals.index')])
                : redirect()->route('rentals.index')->with('success', __('messages.rental_created'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rental creation failed: ' . $e->getMessage());

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => __('messages.rental_create_failed')], 422)
                : redirect()->route('rentals.create')->with('error', __('messages.rental_create_failed'));
        }
    }

    public function show(Rental $rental)
    {
        return view('rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        $cars = Car::where('status', 'available')
            ->orWhere('id', $rental->car_id)
            ->get();
        $customers = Customer::all();

        return view('rentals.edit', compact('rental', 'cars', 'customers'));
    }

    public function update(Request $request, Rental $rental)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'customer_id' => 'required|exists:customers,id',
            'price_per_hour' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'rental_type' => 'required|in:daily,weekly,monthly',
            'notes' => 'nullable|string'
        ]);

        if ($rental->car_id != $request->car_id) {
            $rental->car->update(['status' => 'available']);
            Car::findOrFail($request->car_id)->update(['status' => 'rented']);
        }

        $rental->update($request->all());

        return redirect()->route('rentals.index')->with('success', 'Rental updated successfully');
    }

    public function returnCar(Request $request, Rental $rental)
    {
        $request->validate([
            'end_time' => 'required|date',
            'additional_payment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            if ($rental->status !== 'active') {
                throw new \Exception(__('messages.rental_already_completed'));
            }

            $rental->actual_end_time = Carbon::parse($request->end_time);
            $rental->actual_amount = $rental->calculateActualAmount();

            if ($request->filled('additional_payment') && $request->additional_payment > 0) {
                $rental->paid_amount += $request->additional_payment;

                Account::create([
                    'type' => 'income',
                    'amount' => $request->additional_payment,
                    'description' => __('messages.rental_additional_payment') . ' - ID: ' . $rental->id,
                    'date' => now()
                ]);
            }

            $details = $this->calculateRentalDetails($rental, $request->end_time);

            $rental->update([
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'status' => 'completed',
                'total_cost' => $details['total_cost'],
                'tax_amount' => $details['tax_amount']
            ]);

            $rental->car->update(['status' => 'available']);

            if ($rental->driver) {
                $rental->driver->update(['status' => 'available']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.rental_returned_successfully'),
                'details' => $details
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error returning rental: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    private function calculateRentalDetails($rental, $returnDate)
    {
        $settings = \App\Models\Setting::first();
        $taxPercentage = $settings->tax_percentage ?? 15.0;

        $start = Carbon::parse($rental->start_time);
        $end = Carbon::parse($returnDate);
        $days = $start->diffInDays($end) + 1;

        $carCost = $rental->car->daily_rate * $days;
        $driverCost = $rental->driver ? $rental->driver->daily_rate * $days : 0;
        $base = $carCost + $driverCost;

        $percentage = $rental->car->rental_percentage ?? 0;
        $baseWithPerc = $base + ($base * ($percentage / 100));
        $taxAmount = $baseWithPerc * ($taxPercentage / 100);
        $totalCost = $baseWithPerc + $taxAmount;

        return [
            'days' => $days,
            'car_cost' => $carCost,
            'driver_cost' => $driverCost,
            'rental_percentage' => $percentage,
            'base_cost' => $base,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total_cost' => $totalCost
        ];
    }

    public function destroy(Rental $rental)
    {
        try {
            DB::beginTransaction();

            $rental->car->update(['status' => 'available']);

            if ($rental->driver_id) {
                $rental->driver->update(['status' => 'available']);
            }

            $rental->delete();

            DB::commit();

            return redirect()->route('rentals.index')->with('success', __('messages.rental_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('rentals.index')->with('error', __('messages.rental_delete_failed'));
        }
    }

    public function invoice(Rental $rental)
    {
        return $this->getInvoice($rental);
    }

    public function getInvoice(Rental $rental)
    {
        $rental->load(['car', 'customer', 'driver']);

        $total = $rental->actual_amount ?? $rental->expected_amount;
        $remaining = $rental->calculateRemainingAmount();

        return view('rentals.invoice', compact('rental', 'total', 'remaining'));
    }

    public function getRentalsData()
    {
        $rentals = Rental::with(['car', 'customer', 'driver'])->get();

        return DataTables::of($rentals)
            ->addColumn('car', fn($r) => $r->car->brand . ' ' . $r->car->model)
            ->addColumn('customer', fn($r) => $r->customer->name)
            ->addColumn('driver', fn($r) => $r->driver ? $r->driver->name : '-')
            ->addColumn('start_time', fn($r) => $r->start_time->format('Y-m-d H:i'))
            ->addColumn('end_time', fn($r) => $r->end_time ? $r->end_time->format('Y-m-d H:i') : '-')
            ->addColumn('total_cost', fn($r) => number_format($r->total_cost, 2))
            ->addColumn('status', fn($r) => $r->status_text)
            ->addColumn('action', function ($r) {
                $buttons = '';
                if ($r->status === 'active' && auth()->user()->can('rental-return')) {
                    $buttons .= '<button class="btn btn-sm btn-success return-rental mx-1" data-id="' . $r->id . '"><i class="fas fa-undo"></i> ' . __('messages.return_car') . '</button>';
                }
                if (auth()->user()->can('rental-edit')) {
                    $buttons .= '<a href="' . route('rentals.edit', $r->id) . '" class="btn btn-sm btn-info mx-1"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</a>';
                }
                if (auth()->user()->can('rental-delete')) {
                    $buttons .= '<button class="btn btn-sm btn-danger delete-rental mx-1" data-id="' . $r->id . '"><i class="fas fa-trash"></i> ' . __('messages.delete') . '</button>';
                }
                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'nullable|email|max:255|unique:customers',
            'address' => 'nullable|string',
            'id_number' => 'required|string|max:20|unique:customers',
            'id_type' => 'required|in:national_id,iqama,passport'
        ]);

        try {
            $customer = Customer::create($validated);

            return response()->json([
                'success' => true,
                'message' => __('messages.customer_created'),
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.customer_create_failed')
            ], 422);
        }
    }
}
