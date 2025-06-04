<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Setting;
use App\Models\Route;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:rental-list')->only(['index', 'show']);
        $this->middleware('permission:rental-create')->only(['create', 'store', 'storeCustomer', 'storeDriver', 'storeRoute']);
        $this->middleware('permission:rental-edit')->only(['edit', 'update']);
    }

     // عرض الصفحة الرئيسية للإيجارات
     public function index()
     {
         return view('rentals.index');
     }
 


     public function getRentalsData(Request $request)
     {
         $rentals = Rental::with(['car', 'customer', 'driver'])->latest()->get();
     
         return DataTables::of($rentals)
             ->addColumn('car_plate', function ($r) {
                 return $r->car ? $r->car->plate_number : '-';
             })
             ->addColumn('customer_name', function ($r) {
                 return $r->customer ? $r->customer->name : '-';
             })
             ->addColumn('start_time', function ($r) {
                 return optional($r->start_time)->format('Y-m-d H:i') ?: '-';
             })
             ->addColumn('end_time', function ($r) {
                 return optional($r->expected_end_time)->format('Y-m-d H:i') ?: '-';
             })
             ->addColumn('paid_amount', function ($r) {
                 return number_format($r->paid_amount, 2);
             })
             ->addColumn('total_amount', function ($r) {
                 return number_format($r->total_amount, 2);
             })
             ->addColumn('status_badge', function ($r) {
                 $badgeClass = match ($r->status) {
                     'active' => 'success',
                     'completed' => 'secondary',
                     default => 'danger'
                 };
                 return "<span class='badge badge-$badgeClass'>{$r->status_text}</span>";
             })
             ->addColumn('action', function ($r) {
                 $actions = [];
     
                 if (auth()->user()->can('rental-edit')) {
                     $actions[] = '<a href="' . route('rentals.edit', $r->id) . '" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> تعديل </a>';
                 }
     
                 if ($r->status === 'active' && auth()->user()->can('rental-return')) {
                     $actions[] = '<a href="#" class="btn btn-sm btn-warning return-rental" data-id="' . $r->id . '"><i class="fas fa-redo-alt"></i> إرجاع السيارة </a>';
                 }
     
                 if (auth()->user()->can('rental-list')) {
                     $actions[] = '<a href="' . route('rentals.invoice', $r->id) . '" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-print"></i> طباعة الفاتورة </a>';
                 }
     
                 return implode(' ', $actions);
             })
             ->rawColumns(['status_badge', 'action'])
             ->make(true);
     }
     


     public function create()
     {
        $cars = Car::all();
        $customers = Customer::all();
        $drivers = Driver::all();
        $routes = Route::all();

        return view('rentals.create', compact('cars', 'customers', 'drivers', 'routes'));
     }  
 

  // RentalController.php

  public function store(Request $request)
  {
   
 
      $validator = Validator::make($request->all(), [
          'car_id' => 'required|exists:cars,id',
          'customer_id' => 'required|exists:customers,id',
          'driver_id' => 'nullable|exists:drivers,id',
          'start_time' => 'required|date',
          'expected_end_time' => 'required|date',
          'rental_mode' => 'required|in:normal,route',
          'total_amount' => 'required|numeric|min:0',
          'paid_amount' => 'nullable|numeric|min:0',
          'route_id' => 'nullable|required_if:rental_mode,route|exists:routes,id',
          'price_per_day' => 'nullable|numeric|min:0',
          'driver_price_per_day' => 'nullable|numeric|min:0'
      ]);

      if ($validator->fails()) {
          return response()->json(['errors' => $validator->errors()], 422);
      }

      DB::beginTransaction();

      try {
          $validated = $validator->validated();

          // إنشاء الإيجار
          $rental = Rental::create([
              'car_id' => $validated['car_id'],
              'customer_id' => $validated['customer_id'],
              'driver_id' => $validated['driver_id'] ?? null,
              'start_time' => $validated['start_time'],
              'expected_end_time' => $validated['expected_end_time'] ?? null,
              'price_per_day' => $validated['price_per_day'] ?? 0,
              'driver_price_per_day' => $validated['driver_price_per_day'] ?? 0,
              'total_amount' => $validated['total_amount'] ?? 0,
              'expected_amount' => $validated['total_amount'] ?? 0,
              'paid_amount' => $validated['paid_amount'] ?? 0,
              'rental_mode' => $validated['rental_mode'],
              'status' => 'active',
              'route_id' => $validated['route_id'] ?? null
          ]);

       

          // تحديث حالة السيارة والسائق
          $rental->car->update(['status' => 'rented']);
          if ($rental->driver_id) {
              $rental->driver->update(['status' => 'assigned']);
          }

          DB::commit();

          if ($request->paid_amount > 0) {
            Account::create([
                'type' => 'advance_payment',
                'amount' => $request->paid_amount,
                'description' => __('messages.advance_payment_for_rental_by', ['customer' => $rental->customer->name]),
                'date' => now(),
             
            ]);
        }

          return redirect()->route('rentals.index')->with('success', __('messages.rental_created'));

      } catch (\Exception $e) {
          DB::rollBack();
          \Log::error('Rental creation failed: ' . $e->getMessage());
          return back()->withInput()->with('error', $e->getMessage());
      }
  }
  






    public function update(Request $request, Rental $rental)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|exists:cars,id',
                'customer_id' => 'required|exists:customers,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'start_time' => 'required|date',
                'expected_end_time' => 'nullable|date',
                'rental_mode' => 'required|in:normal,route',
                'base_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'route_id' => 'nullable|numeric|exists:routes,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();

            // تحديث بيانات الإيجار
            $rental->update([
                'car_id' => $validated['car_id'],
                'customer_id' => $validated['customer_id'],
                'driver_id' => $validated['driver_id'] ?? null,
                'start_time' => $validated['start_time'],
                'expected_end_time' => $validated['expected_end_time'] ?? null,
                'base_amount' => $validated['base_amount'],
                'total_amount' => $validated['total_amount'],
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'rental_mode' => $validated['rental_mode'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // تحديث خطوط السير
            if ($validated['rental_mode'] === 'route' && !empty($validated['route_id'])) {
                $rental->routes()->sync($validated['route_id']);
            } else {
                $rental->routes()->detach();
            }

            // تحديث حالة السيارة والسائق
            $rental->car->update(['status' => 'rented']);
            if ($rental->driver_id) {
                $rental->driver->update(['status' => 'assigned']);
            }

            DB::commit();

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => __('messages.rental_updated'), 'redirect' => route('rentals.index')])
                : redirect()->route('rentals.index')->with('success', __('messages.rental_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rental update failed: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 500)
                : back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function getInvoice(Rental $rental)
    {
        $rental->load(['car', 'customer', 'driver']);

        $total = $rental->actual_amount ?? $rental->expected_amount;
        $remaining = $rental->calculateRemainingAmount();

        return view('rentals.invoice', compact('rental', 'total', 'remaining'))->render();
    }

    public function calculateTotal(Request $request)
    {
        $data = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'rental_mode' => 'required|in:normal,route',
            'rental_period' => 'required_if:rental_mode,normal|in:daily,weekly,monthly',
            'driver_option' => 'required_if:rental_mode,normal|in:with_driver,without_driver',
            'driver_id' => 'nullable|exists:drivers,id',
            'route_id' => 'nullable|numeric|exists:routes,id',
        ]);

        try {
            $car = Car::findOrFail($data['car_id']);
            $startDate = Carbon::parse($data['start_time']);
            $endDate = Carbon::parse($data['end_time']);
            $days = $startDate->diffInDays($endDate);

            $baseAmount = 0;

            if ($data['rental_mode'] === 'normal') {
                switch ($data['rental_period']) {
                    case 'weekly':
                        $baseAmount = $car->weekly_rate * ceil($days / 7);
                        break;
                    case 'monthly':
                        $baseAmount = $car->monthly_rate * ceil($days / 30);
                        break;
                    default:
                        $baseAmount = $car->daily_rate * max(1, $days);
                        break;
                }

                // Driver Cost
                if ($data['driver_option'] === 'with_driver' && !empty($data['driver_id'])) {
                    $driver = Driver::find($data['driver_id']);
                    if ($driver) {
                        $baseAmount += $driver->daily_rate * max(1, $days);
                    }
                }
            } else {
                // Route-based
                $routes = Route::where('id', $rental->route_id)->get();
                $baseAmount = $routes->sum(function ($route) use ($car) {
                    return $car->is_internal ? $route->internal_cost : $route->external_cost;
                });
            }

            // Apply Percentage if any
            $percentageAmount = 0;
            if ($car->has_rental_percentage && $car->rental_percentage > 0) {
                $percentageAmount = ($baseAmount * $car->rental_percentage) / 100;
            }

            $totalAmount = $baseAmount + $percentageAmount;
            $driverCost = $request->driver_cost ?? 0;
            return response()->json([
                'success' => true,
                'base_amount' => round($baseAmount, 2),
                'percentage_amount' => round($percentageAmount, 2),
                'total_amount' => round($baseAmount + $driverCost + $percentageAmount + $taxAmount, 2),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

// إرجاع السيارة (Return Car)
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

        $startDate = Carbon::parse($rental->start_time);
        $endDate = Carbon::parse($request->end_time);
        $days = $startDate->diffInDays($endDate) + 1;

        $baseAmount = 0;
        $driverCost = 0;

        if ($rental->rental_mode === 'normal') {
            $baseAmount = $rental->price_per_day * $days;
            if ($rental->driver) {
                $driverCost = ($rental->driver_price_per_day ?? $rental->driver->daily_rate) * $days;
            }
        } else {
            $baseAmount = $rental->route?->internal_cost ?? 0;
            $driverCost = 0;
        }

        $percentageAmount = 0;
        if ($rental->car && $rental->car->has_rental_percentage && $rental->car->rental_percentage > 0) {
            $percentageAmount = ($baseAmount + $driverCost) * ($rental->car->rental_percentage / 100);
        }

        $settings = \App\Models\Setting::first();
        $taxPercentage = $settings?->tax_percentage ?? 0;
        $taxAmount = $baseAmount + $driverCost ;
        $totalWithTax = $baseAmount + $driverCost;

        $additionalPayment = $request->additional_payment ?? 0;
        $totalWithExtra = $totalWithTax + $additionalPayment;

        // تحديث بيانات الإيجار
        $rental->update([
            'actual_end_time' => $request->end_time,
            'actual_amount' => $totalWithTax,
            'paid_amount' => $totalWithTax + $additionalPayment,
            'remaining_amount' => 0,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);

        // تحديث حالة السيارة والسائق
        $rental->car->update(['status' => 'available']);
        if ($rental->driver_id) {
            $rental->driver->update(['status' => 'available']);
        }

        // تسجيل الإيراد
        Account::create([
            'type' => 'income',
            'amount' => $totalWithTax + $additionalPayment,
            'description' => __('messages.full_rental_payment_for') . ' #' . $rental->id,
            'date' => now(),
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => __('messages.rental_returned'),
            'redirect' => route('rentals.index')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}   

 // عرض تفاصيل الفاتورة
    public function invoice(Rental $rental)
    {
        $rental->load(['car', 'customer', 'driver', 'routes']);
        // حساب التفاصيل بنفس الدالة الموحدة
        $details = $this->calculateRentalAmounts($rental);
        return view('rentals.invoice', compact('rental', 'details'));
    }


     // عرض صفحة الإرجاع
    public function showReturnForm(Request $request)
    {
        $rental = Rental::with(['car', 'customer', 'driver', 'route'])->findOrFail($request->rental_id);

        if ($rental->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => __('messages.rental_already_completed')
            ]);
        }

        $details = $this->calculateRentalAmounts($rental);
        $details['id'] = $rental->id;
        $details['type'] = $rental->rental_mode === 'normal' ? 'إيجار عادي' : 'خط سير';
        $details['rental_percentage'] = $rental->car->rental_percentage ?? 0;
        $details['tax_percentage'] = 0;
        $details['notes'] = $rental->notes ?? '';

        return response()->json([
            'success' => true,
            'details' => $details
        ]);
    }

    /**
     * حساب تفاصيل الإيجار (موحد)
     */
    private function calculateRentalAmounts($rental, $endTime = null)
    {
        $startDate = Carbon::parse($rental->start_time);
        $endDate = $endTime ? Carbon::parse($endTime) : ($rental->actual_end_time ?? $rental->expected_end_time);
        $days = $startDate->diffInDays($endDate) + 1; // توحيد الحساب: +1 فقط

        if ($rental->rental_mode === 'normal') {
            $baseAmount = $rental->price_per_day  ;
            $driverCost = $rental->driver->daily_rate * $days;
        } else {
            $baseAmount = $rental->route?->internal_cost ?? 0;
            $driverCost = 0;
        }

        $percentageAmount = 0;
        if ($rental->car && $rental->car->has_rental_percentage && $rental->car->rental_percentage > 0) {
            $percentageAmount = $baseAmount + $driverCost;
        }
        $rentaltype = $rental->rental_mode;

        $taxPercentage = 0;
        $taxAmount = 0;
        $totalWithTax = $baseAmount + ($driverCost* $days);
        $remaining = $totalWithTax - $rental->paid_amount;

        return [
            'days' => $days,
            'base_cost' => $baseAmount,
            'driver_cost' => $driverCost,
            'percentage_amount' => $percentageAmount,
            'tax_amount' => $taxAmount,
            'total_cost' => $totalWithTax,
            'paid_amount' => $rental->paid_amount,
            'remaining_amount' => $remaining,
            'rental_type' => $rentaltype,
        ];
    }

    public function calculateReturnDetails(Request $request)
    {
        // dd($request->all());
        $rental = Rental::with(['car', 'customer', 'driver', 'route'])->findOrFail($request->rental_id);

        if ($rental->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => __('messages.rental_already_completed')
            ]);
        }

        $details = $this->calculateRentalAmounts($rental, $request->end_time);

        return response()->json([
            'success' => true,
            'details' => $details
        ]);
    }
}