<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\SparePartSale;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SparePartSaleController extends Controller
{
    public function index()
    {
        $spareParts = SparePart::where('quantity', '>', 0)->get();
        return view('spare-part-sales.index', compact('spareParts'));
    }

    public function getSparePartSalesData(Request $request)
    {
        if ($request->ajax()) {
            $data = SparePartSale::with('sparePart')->select(['id', 'spare_part_id', 'quantity', 'total_price', 'sale_date']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('spare_part_name', function ($row) {
                    return $row->sparePart->name;
                })
                ->addColumn('action', function ($row) {
                    $deleteBtn = '<form action="'.route('spare-part-sales.destroy', $row->id).'" method="POST" style="display:inline-block;">'
                                .csrf_field().method_field('DELETE')
                                .'<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> '.trans('messages.delete').'</button>'
                                .'</form>';
                    return $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'spare_parts' => 'required|array',
                'spare_parts.*.spare_part_id' => 'required_without:spare_parts.*.spare_part_name',
                'spare_parts.*.spare_part_name' => 'required_without:spare_parts.*.spare_part_id|string|max:255',
                'spare_parts.*.quantity' => 'required|integer|min:1',
                'spare_parts.*.selling_price' => 'required|numeric|min:0',
            ]);

            $totalSaleCost = 0;

            foreach ($request->spare_parts as $item) {
                $sparePart = null;

                if (isset($item['spare_part_name'])) {
                    // Create a new spare part if spare_part_name is provided
                    $sparePart = SparePart::create([
                        'name' => $item['spare_part_name'],
                        'quantity' => $item['quantity'], // Initial quantity for the new spare part
                        'purchase_price' => 0, // You might want to add a default or ask for it in the form
                        'selling_price' => $item['selling_price'],
                        'description' => '',
                    ]);
                } else {
                    // Use existing spare part
                    $sparePart = SparePart::findOrFail($item['spare_part_id']);

                    // Check if enough quantity is available
                    if ($sparePart->quantity < $item['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.insufficient_quantity') . ': ' . $sparePart->name
                        ], 400);
                    }

                    // Update spare part quantity and selling price
                    $sparePart->quantity -= $item['quantity'];
                    $sparePart->selling_price = $item['selling_price'];
                    $sparePart->save();
                }

                // Create the sale record
                $sale = SparePartSale::create([
                    'spare_part_id' => $sparePart->id,
                    'quantity' => $item['quantity'],
                    'total_price' => $item['selling_price'] * $item['quantity'], // Changed from sale_price to total_price
                    'sale_date' => now(),
                ]);

                $totalSaleCost += $sale->total_price; // Changed from sale_price to total_price
            }

            // Record the total sale as income in the accounts table
            Account::create([
                'type' => 'income',
                'amount' => $totalSaleCost,
                'description' => 'Sale of spare parts',
                'date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.spare_part_sale_added')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in SparePartSalesController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sale = SparePartSale::findOrFail($id);
        $sparePart = SparePart::find($sale->spare_part_id);
        $sparePart->increment('quantity', $sale->quantity);
        $sale->delete();
        return redirect()->route('spare-part-sales.index')->with('success', trans('messages.sale_deleted_successfully'));
    }
}
