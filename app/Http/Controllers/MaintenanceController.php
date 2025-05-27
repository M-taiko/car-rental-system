<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Account;
use App\Models\SparePart;
use App\Models\MaintenancePart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-maintenance')->only(['index', 'show']);
        $this->middleware('permission:create-maintenance')->only(['create', 'store']);
        $this->middleware('permission:complete-maintenance')->only(['complete']);
        $this->middleware('permission:delete-maintenance')->only(['destroy']);
    }

    public function index()
    {
        $cars = Car::all();
        $customers = Customer::all();
        $spareParts = SparePart::where('quantity', '>', 0)->get(); // جلب قطع الغيار المتاحة فقط
        return view('maintenance.index', compact('cars', 'customers', 'spareParts'));
    }

    public function getMaintenanceData(Request $request)
    {
        try {
            // التحقق من إن الطلب هو AJAX
            if (!$request->ajax()) {
                return response()->json(['error' => 'Invalid request'], 400);
            }

            // التحقق من إن المستخدم مسجل دخول
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
            }

            // التحقق من صلاحية عرض الصيانات
            if (!auth()->user()->hasPermissionTo('view-maintenance')) {
                return response()->json(['error' => 'Unauthorized. You do not have permission to view maintenance records.'], 403);
            }

            $maintenances = Maintenance::with(['car', 'customer'])->select('maintenance.*');

            return DataTables::of($maintenances)
                ->addColumn('car_info', function ($maintenance) {
                    return $maintenance->car->brand . ' ' . $maintenance->car->model . ' (' . $maintenance->car->plate_number . ')';
                })
                ->addColumn('customer_name', function ($maintenance) {
                    return $maintenance->customer ? $maintenance->customer->name : 'صيانة داخلية';
                })
                ->addColumn('customer_phone', function ($maintenance) {
                    return $maintenance->customer ? $maintenance->customer->phone : '-';
                })
                ->addColumn('type', function ($maintenance) {
                    return $maintenance->type === 'internal' ? __('messages.internal') : __('messages.customer');
                })
                ->addColumn('status', function ($maintenance) {
                    return $maintenance->status === 'completed'
                        ? '<span class="badge badge-success">' . __('messages.completed') . '</span>'
                        : '<span class="badge badge-warning">' . __('messages.pending') . '</span>';
                })
                ->addColumn('action', function ($maintenance) {
                    $actions = '';
                    // زر "Complete" لو الصيانة Pending و المستخدم عنده صلاحية complete-maintenance
                    if ($maintenance->status === 'pending' && auth()->user()->hasPermissionTo('complete-maintenance')) {
                        $actions .= '<button class="btn btn-sm btn-success complete-maintenance" data-id="' . $maintenance->id . '"><i class="fas fa-check"></i> ' . __('messages.complete') . '</button> ';
                    }
                    // زر "Invoice" لو المستخدم عنده صلاحية view-maintenance-invoice
                    if (auth()->user()->hasPermissionTo('view-maintenance-invoice')) {
                        $actions .= '<a href="' . route('maintenance.invoice', $maintenance->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-file-invoice"></i> ' . __('messages.invoice') . '</a> ';
                    }
                    // زر "Delete" لو المستخدم عنده صلاحية delete-maintenance
                    if (auth()->user()->hasPermissionTo('delete-maintenance')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-maintenance" data-id="' . $maintenance->id . '"><i class="fas fa-trash"></i> ' . __('messages.delete') . '</button>';
                    }
                    return $actions ?: '<span>' . __('messages.no_actions') . '</span>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@getMaintenanceData: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An error occurred while fetching data: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'type' => 'required|in:internal,customer',
                'customer_id' => 'required_if:type,customer|nullable|exists:customers,id',
                'cost' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'use_parts' => 'required|in:yes,no',
                'parts' => 'required_if:use_parts,yes|array',
                'parts.*.spare_part_id' => 'required_if:use_parts,yes|exists:spare_parts,id',
                'parts.*.quantity' => 'required_if:use_parts,yes|integer|min:1',
            ]);

            // حساب إجمالي سعر قطع الغيار
            $partsTotalCost = 0;
            if ($request->use_parts === 'yes' && !empty($request->parts)) {
                foreach ($request->parts as $part) {
                    $sparePart = SparePart::findOrFail($part['spare_part_id']);
                    // التحقق من الكمية المتاحة
                    if ($sparePart->quantity < $part['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.insufficient_quantity', ['name' => $sparePart->name])
                        ], 400);
                    }
                    $partsTotalCost += $sparePart->selling_price * $part['quantity'];
                }
            }

            // السعر الإجمالي = سعر الصيانة + سعر قطع الغيار
            $totalCost = $request->cost + $partsTotalCost;

            // إنشاء سجل الصيانة
            $maintenance = Maintenance::create([
                'car_id' => $request->car_id,
                'type' => $request->type,
                'customer_id' => $request->type === 'customer' ? $request->customer_id : null,
                'cost' => $totalCost,
                'description' => $request->description,
                'date' => $request->date,
                'status' => 'pending',
            ]);

            // إضافة قطع الغيار وخصم الكمية من المخزون
            if ($request->use_parts === 'yes' && !empty($request->parts)) {
                foreach ($request->parts as $part) {
                    $sparePart = SparePart::findOrFail($part['spare_part_id']);
                    // خصم الكمية من المخزون
                    $sparePart->decrement('quantity', $part['quantity']);
                    // إضافة القطعة إلى الصيانة
                    $maintenance->parts()->create([
                        'spare_part_id' => $part['spare_part_id'],
                        'quantity' => $part['quantity'],
                    ]);
                }
            }

            // إنشاء مصروف للصيانة
            if ($maintenance->type === 'internal') {
                Account::create([
                    'amount' => $maintenance->cost,
                    'type' => 'expense',
                    'description' => 'مصروف صيانة للسيارة: ' . $maintenance->car->brand . ' ' . $maintenance->car->model . ' (المعرف: ' . $maintenance->id . ')',
                    'date' => now()
                ]);
            }

            return response()->json(['success' => true, 'message' => __('messages.maintenance_added')]);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeCustomer(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20|unique:customers,phone',
            ]);

            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            return response()->json(['success' => true, 'customer' => $customer]);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@storeCustomer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);

            if ($maintenance->status === 'completed') {
                return response()->json(['success' => false, 'message' => __('messages.maintenance_already_completed')]);
            }

            // تحديث حالة الصيانة إلى مكتملة
            $maintenance->update([
                'status' => 'completed',
                'end_date' => now()
            ]);

            // تحديث حالة السيارة إلى متاحة
            $maintenance->car->update(['status' => 'available']);

            // حساب تكلفة قطع الغيار (سعر الشراء للمصروفات، وسعر البيع للإيرادات)
            $partsPurchaseCost = 0; // تكلفة الشراء (للمصروفات)
            $partsSellingCost = 0;  // تكلفة البيع (للإيرادات)
            foreach ($maintenance->parts as $part) {
                $partsPurchaseCost += $part->sparePart->purchase_price * $part->quantity;
                $partsSellingCost += $part->sparePart->selling_price * $part->quantity;
            }

            // تسجيل الصيانة في جدول الحسابات بناءً على نوع الصيانة
            if ($maintenance->type === 'internal') {
                // مصروفات الصيانة الداخلية (سعر الصيانة + تكلفة شراء قطع الغيار)
                Account::create([
                    'type' => 'expense',
                    'amount' => $maintenance->cost - $partsSellingCost + $partsPurchaseCost,
                    'description' => "Internal maintenance expense (Maintenance ID: {$maintenance->id})",
                    'date' => now(),
                ]);
            } else {
                // إيرادات الصيانة الخارجية (سعر الصيانة + سعر بيع قطع الغيار)
                Account::create([
                    'type' => 'income',
                    'amount' => $maintenance->cost,
                    'description' => "Customer maintenance income (Maintenance ID: {$maintenance->id})",
                    'date' => now(),
                ]);
                // مصروفات قطع الغيار (سعر الشراء)
                if ($partsPurchaseCost > 0) {
                    Account::create([
                        'type' => 'expense',
                        'amount' => $partsPurchaseCost,
                        'description' => "Spare parts cost for customer maintenance (Maintenance ID: {$maintenance->id})",
                        'date' => now(),
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => __('messages.maintenance_completed')]);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@complete: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function sparePartsProfitReport()
    {
        $maintenanceParts = MaintenancePart::whereHas('maintenance', function ($query) {
            $query->where('type', 'customer');
        })->with('sparePart')->get();

        $report = [];
        foreach ($maintenanceParts as $part) {
            $sparePart = $part->sparePart;
            $quantity = $part->quantity;
            $profitPerUnit = $sparePart->selling_price - $sparePart->purchase_price;
            $totalProfit = $profitPerUnit * $quantity;

            $report[] = [
                'spare_part_name' => $sparePart->name,
                'quantity_used' => $quantity,
                'purchase_price' => $sparePart->purchase_price,
                'selling_price' => $sparePart->selling_price,
                'profit_per_unit' => $profitPerUnit,
                'total_profit' => $totalProfit,
            ];
        }

        return view('maintenance.spare_parts_profit_report', compact('report'));
    }

    public function destroy($id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->delete();
            return response()->json(['success' => true, 'message' => __('messages.maintenance_deleted')]);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@destroy: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getInvoice($id)
    {
        try {
            $maintenance = Maintenance::with(['car', 'customer'])->findOrFail($id);
            return view('maintenance.invoice', compact('maintenance'));
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@getInvoice: ' . $e->getMessage());
            return redirect()->route('maintenance.index')->with('error', $e->getMessage());
        }
    }
}
