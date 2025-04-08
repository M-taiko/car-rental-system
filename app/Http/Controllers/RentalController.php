<?php
namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Maintenance;
use App\Models\Expense;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RentalController extends Controller
{
    public function index()
    {
        $bikes = Bike::where('status', 'available')->get();
        $customers = Customer::all();
        return view('rentals.index', compact('bikes', 'customers'));
    }

    public function getRentalsData(Request $request)
    {
        if ($request->ajax()) {
            $data = Rental::with('bike', 'customer')->select(['id', 'bike_id', 'customer_id', 'customer_name', 'price_per_hour', 'start_time', 'original_start_time', 'end_time', 'total_cost', 'status']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bike_name', function ($row) {
                    return $row->bike->name;
                })
                ->addColumn('user_name', function ($row) {
                    return $row->customer_name ?? ($row->customer ? $row->customer->name : '-');
                })
                ->addColumn('start_date', function ($row) {
                    return $row->original_start_time ? date('Y-m-d', strtotime($row->original_start_time)) : '-';
                })
                ->addColumn('start_time', function ($row) {
                    return $row->original_start_time ? date('h:i:s A', strtotime($row->original_start_time)) : '-';
                })
                ->addColumn('end_date', function ($row) {
                    return $row->end_time ? date('Y-m-d', strtotime($row->end_time)) : '-';
                })
                ->addColumn('end_time', function ($row) {
                    return $row->end_time ? date('h:i:s A', strtotime($row->end_time)) : '-';
                })
                ->addColumn('hours', function ($row) {
                    if ($row->original_start_time && $row->end_time) {
                        $start = strtotime($row->original_start_time);
                        $end = strtotime($row->end_time);
                        $hours = round(($end - $start) / 3600);
                        return $hours;
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $returnBtn = $row->status == 'ongoing' && !$row->end_time ?
                        '<button class="btn btn-warning btn-sm return-bike-btn" data-id="'.$row->id.'"><i class="fas fa-undo"></i> '.trans('messages.return_bike').'</button>' : '';
                    $calculateBtn = $row->status == 'ongoing' && $row->end_time ?
                        '<form action="'.route('rentals.calculateCost', $row->id).'" method="POST" style="display:inline-block;">'
                        .csrf_field()
                        .'<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-calculator"></i> '.trans('messages.calculate_cost').'</button>'
                        .'</form>' : '';
                    $viewInvoiceBtn = $row->status == 'completed' ?
                        '<a href="'.route('rentals.getInvoice', $row->id).'" class="btn btn-info btn-sm"><i class="fas fa-file-invoice"></i> '.trans('messages.view_invoice').'</a>' : '';
                    $deleteBtn = '<form action="'.route('rentals.destroy', $row->id).'" method="POST" style="display:inline-block;">'
                                .csrf_field().method_field('DELETE')
                                .'<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> '.trans('messages.delete').'</button>'
                                .'</form>';
                    return $returnBtn . ' ' . $calculateBtn . ' ' . $viewInvoiceBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bike_id' => 'required|exists:bikes,id',
            'customer_id' => 'nullable|exists:customers,id',
            'price_per_hour' => 'required|numeric|min:0',
            'start_time' => 'required',
        ]);

        $bike = Bike::findOrFail($request->bike_id);
        if ($bike->status == 'rented') {
            return redirect()->route('rentals.index')->with('error', trans('messages.bike_already_rented'));
        }

        $customerName = null;
        $customerId = $request->customer_id;
        if ($customerId) {
            $customer = Customer::find($customerId);
            $customerName = $customer->name;
        } else {
            $customerName = trans('messages.cash_customer');
            $customerId = null;
        }

        $startTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $request->start_time));

        Rental::create([
            'bike_id' => $request->bike_id,
            'customer_id' => $customerId,
            'customer_name' => $customerName,
            'price_per_hour' => $request->price_per_hour,
            'start_time' => $startTime,
            'original_start_time' => $startTime, // احفظ نسخة ثابتة
            'status' => 'ongoing',
        ]);

        $bike->update(['status' => 'rented']);
        return redirect()->route('rentals.index')->with('success', trans('messages.rental_added_successfully'));
    }


    public function returnBike(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);
        if ($rental->status == 'completed') {
            return redirect()->route('rentals.index')->with('error', trans('messages.rental_already_completed'));
        }

        if ($rental->end_time) {
            return redirect()->route('rentals.index')->with('error', trans('messages.bike_already_returned'));
        }

        $request->validate([
            'end_time' => 'required',
        ]);

        \Log::info('Start Time Before Return: ' . $rental->start_time);

        $endTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $request->end_time));
        $rental->end_time = $endTime;

        // تأكد إن start_time ما بيتغيرش
        $rental->save(['timestamps' => false]); // تعطيل الـ timestamps التلقائية

        $rental = $rental->fresh();
        \Log::info('Start Time After Return: ' . $rental->start_time);

        return redirect()->route('rentals.index')->with('success', trans('messages.bike_returned_successfully'));
    }

    public function calculateCost(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);
        if ($rental->status == 'completed') {
            return redirect()->route('rentals.index')->with('error', trans('messages.rentalAlreadyCompleted'));
        }

        if (!$rental->end_time) {
            return redirect()->route('rentals.index')->with('error', trans('messages.bike_not_returned_yet'));
        }

        $startTime = $rental->original_start_time; // لو استخدمنا original_start_time، استبدلها هنا
        $endTime = $rental->end_time;

        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $hours = round(($endTimestamp - $startTimestamp) / 3600);

        $rental->total_cost = $hours * $rental->price_per_hour;
        $rental->status = 'completed';
        $rental->save();

        $bike = Bike::find($rental->bike_id);
        $bike->update(['status' => 'available']);

        // تسجيل الدخل في الخزينة
        Account::create([
            'type' => 'income',
            'amount' => $rental->total_cost,
            'description' => trans('messages.rental_invoice_description', ['id' => $rental->id, 'user' => $rental->customer_name ?? ($rental->customer ? $rental->customer->name : '-')]),
            'date' => date('Y-m-d H:i:s'),
        ]);

        $invoice = [
            'rental_id' => $rental->id,
            'bike_name' => $rental->bike->name,
            'user_name' => $rental->customer_name ?? ($rental->customer ? $rental->customer->name : '-'),
            'start_time' => date('Y-m-d h:i:s A', strtotime($rental->start_time)),
            'end_time' => date('Y-m-d h:i:s A', strtotime($rental->end_time)),
            'hours' => $hours,
            'price_per_hour' => $rental->price_per_hour,
            'total_cost' => $rental->total_cost,
        ];

        return redirect()->route('rentals.showInvoice', $rental->id)->with('invoice', $invoice);
    }

    public function getInvoice($id)
    {
        $rental = Rental::findOrFail($id);
        if ($rental->status != 'completed') {
            return redirect()->route('rentals.index')->with('error', trans('messages.rental_not_completed'));
        }

        $startTimestamp = strtotime($rental->original_start_time);
        $endTimestamp = strtotime($rental->end_time);
        $hours = round(($endTimestamp - $startTimestamp) / 3600);

        $invoice = [
            'rental_id' => $rental->id,
            'bike_name' => $rental->bike->name,
            'user_name' => $rental->customer_name ?? ($rental->customer ? $rental->customer->name : '-'),
            'start_time' => date('Y-m-d h:i:s A', strtotime($rental->original_start_time)),
            'end_time' => date('Y-m-d h:i:s A', strtotime($rental->end_time)),
            'hours' => $hours,
            'price_per_hour' => $rental->price_per_hour,
            'total_cost' => $rental->total_cost,
        ];

        return redirect()->route('rentals.showInvoice', $rental->id)->with('invoice', $invoice);
    }

    public function showInvoice($id)
    {
        $invoice = session('invoice');
        if (!$invoice) {
            return redirect()->route('rentals.index')->with('error', trans('messages.invoice_not_found'));
        }

        return view('rentals.invoice', compact('invoice'));
    }


    public function getNotifications()
    {
        $notifications = [];

        // إشعارات الإيجارات الجديدة
        $newRentals = Rental::where('status', 'ongoing')
                            ->where('created_at', '>=', now()->subDays(1))
                            ->with('bike') // التأكد من تحميل العلاقة
                            ->get();
        foreach ($newRentals as $rental) {
            $bikeName = $rental->bike ? $rental->bike->name : 'Unknown Bike'; // التحقق من وجود الدراجة
            $notifications[] = [
                'message' => trans('messages.new_rental_notification', ['id' => $rental->id, 'bike' => $bikeName]),
                'time' => $rental->created_at->diffForHumans(),
                'icon' => 'fas fa-bicycle',
                'color' => 'primary',
            ];
        }

        // إشعارات الصيانة المكتملة
        $completedMaintenance = Maintenance::where('status', 'completed')
                                          ->where('updated_at', '>=', now()->subDays(1))
                                          ->with('bike') // التأكد من تحميل العلاقة
                                          ->get();
        foreach ($completedMaintenance as $maintenance) {
            $bikeName = $maintenance->bike ? $maintenance->bike->name : 'Unknown Bike'; // التحقق من وجود الدراجة
            $notifications[] = [
                'message' => trans('messages.maintenance_completed_notification', ['id' => $maintenance->id, 'bike' => $bikeName]),
                'time' => $maintenance->updated_at->diffForHumans(),
                'icon' => 'fas fa-tools',
                'color' => 'success',
            ];
        }

        // إشعارات المصروفات الجديدة
        $newExpenses = Expense::where('created_at', '>=', now()->subDays(1))
                              ->get();
        foreach ($newExpenses as $expense) {
            $notifications[] = [
                'message' => trans('messages.new_expense_notification', ['amount' => $expense->amount]),
                'time' => $expense->created_at->diffForHumans(),
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'danger',
            ];
        }

        // ترتيب الإشعارات حسب الوقت (الأحدث أولاً)
        usort($notifications, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return $notifications;
    }

    public function destroy($id)
    {
        $rental = Rental::findOrFail($id);
        $bike = Bike::find($rental->bike_id);
        if ($rental->status == 'ongoing') {
            $bike->update(['status' => 'available']);
        }
        $rental->delete();
        return redirect()->route('rentals.index')->with('success', trans('messages.rental_deleted_successfully'));
    }
}
