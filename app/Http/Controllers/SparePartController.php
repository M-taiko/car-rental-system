<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\Account;
use Illuminate\Http\Request;
use DataTables;

class SparePartController extends Controller
{
    public function index()
    {
        $spareParts = SparePart::all();
        return view('spare-parts.index', compact('spareParts'));
    }

    public function getSparePartsData()
    {
        $spareParts = SparePart::select(['id', 'name', 'quantity', 'purchase_price', 'selling_price', 'description']);
        return DataTables::of($spareParts)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button class="btn btn-sm btn-primary edit-spare-part" data-id="' . $row->id . '"><i class="fas fa-edit"></i> ' . __('messages.edit') . '</button>';
                $btn .= ' <button class="btn btn-sm btn-danger delete-spare-part" data-id="' . $row->id . '"><i class="fas fa-trash"></i> ' . __('messages.delete') . '</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'spare_parts' => 'required|array',
                'spare_parts.*.name' => 'required|string|max:255',
                'spare_parts.*.quantity' => 'required|integer|min:0',
                'spare_parts.*.purchase_price' => 'required|numeric|min:0',
                'spare_parts.*.selling_price' => 'required|numeric|min:0',
                'spare_parts.*.description' => 'nullable|string',
            ]);

            $totalPurchaseCost = 0;

            foreach ($request->spare_parts as $sparePartData) {
                $sparePart = SparePart::where('name', $sparePartData['name'])->first();

                if ($sparePart) {
                    // لو المنتج موجود، نحدث الكمية وسعر الشراء وسعر البيع
                    $sparePart->quantity += $sparePartData['quantity'];
                    $sparePart->purchase_price = $sparePartData['purchase_price'];
                    $sparePart->selling_price = $sparePartData['selling_price'];
                    $sparePart->description = $sparePartData['description'];
                    $sparePart->save();
                } else {
                    // لو المنتج جديد، ننشئ سجل جديد
                    $sparePart = SparePart::create([
                        'name' => $sparePartData['name'],
                        'quantity' => $sparePartData['quantity'],
                        'purchase_price' => $sparePartData['purchase_price'],
                        'selling_price' => $sparePartData['selling_price'],
                        'description' => $sparePartData['description'],
                    ]);
                }

                // إضافة سعر الشراء إلى الإجمالي
                $totalPurchaseCost += $sparePartData['purchase_price'] * $sparePartData['quantity'];
            }

            // تسجيل إجمالي سعر الشراء في جدول الحسابات كمصروف
            if ($totalPurchaseCost > 0) {
                Account::create([
                    'type' => 'expense',
                    'amount' => $totalPurchaseCost,
                    'description' => 'Purchase of spare parts',
                    'date' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => __('messages.spare_part_added')]);
        } catch (\Exception $e) {
            \Log::error('Error in SparePartsController@store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $sparePart = SparePart::findOrFail($id);
        return response()->json($sparePart);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $sparePart = SparePart::findOrFail($id);
        $sparePart->update($validated);

        return redirect()->route('spare-parts.index')
            ->with('success', __('messages.spare_part_updated'));
    }

    public function destroy($id)
    {
        $sparePart = SparePart::findOrFail($id);
        $sparePart->delete();

        return response()->json(['success' => true, 'message' => __('messages.spare_part_deleted')]);
    }
}
