<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\Account;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceController extends Controller
{
    public function index()
    {
        $bikes = Bike::all();
        $customers = Customer::all();
        $spareParts = SparePart::where('quantity', '>', 0)->get(); // جلب قطع الغيار المتاحة فقط
        return view('maintenance.index', compact('bikes', 'customers', 'spareParts'));
    }

    public function getMaintenanceData(Request $request)
    {
        try {
            $maintenances = Maintenance::with(['bike', 'customer'])->select('maintenance.*');

            return DataTables::of($maintenances)
                ->addColumn('bike_name', function ($maintenance) {
                    return $maintenance->bike ? $maintenance->bike->name : '-';
                })
                ->addColumn('customer_name', function ($maintenance) {
                    return $maintenance->type === 'customer' && $maintenance->customer ? $maintenance->customer->name : '-';
                })
                ->addColumn('customer_phone', function ($maintenance) {
                    return $maintenance->type === 'customer' && $maintenance->customer ? $maintenance->customer->phone : '-';
                })
                ->addColumn('type', function ($maintenance) {
                    return $maintenance->type === 'internal' ? __('messages.internal') : __('messages.customer');
                })
                ->addColumn('status', function ($maintenance) {
                    return $maintenance->status === 'completed' ? '<span class="badge badge-success">' . __('messages.completed') . '</span>' : '<span class="badge badge-warning">' . __('messages.pending') . '</span>';
                })
                ->addColumn('action', function ($maintenance) {
                    $actions = '';
                    if ($maintenance->status === 'pending') {
                        $actions .= '<button class="btn btn-sm btn-success complete-maintenance" data-id="' . $maintenance->id . '">' . __('messages.complete') . '</button>';
                    }
                    $actions .= ' <a href="' . route('maintenance.invoice', $maintenance->id) . '" class="btn btn-sm btn-primary">' . __('messages.invoice') . '</a>';
                    $actions .= ' <button class="btn btn-sm btn-danger delete-maintenance" data-id="' . $maintenance->id . '">' . __('messages.delete') . '</button>';
                    return $actions;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@getMaintenanceData: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bike_id' => 'required_if:type,internal|nullable|exists:bikes,id',
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
                'bike_id' => $request->bike_id,
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

            // تحديث حالة الصيانة إلى مكتملة
            $maintenance->update(['status' => 'completed']);

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
            $maintenance = Maintenance::with(['bike', 'customer'])->findOrFail($id);
            return view('maintenance.invoice', compact('maintenance'));
        } catch (\Exception $e) {
            \Log::error('Error in MaintenanceController@getInvoice: ' . $e->getMessage());
            return redirect()->route('maintenance.index')->with('error', $e->getMessage());
        }
    }
}
