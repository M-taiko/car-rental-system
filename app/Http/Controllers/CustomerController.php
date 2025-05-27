<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer-list')->only(['index', 'show']);
        $this->middleware('permission:customer-create')->only(['create', 'store']);
        $this->middleware('permission:customer-edit')->only(['edit', 'update']);
        $this->middleware('permission:customer-delete')->only(['destroy']);
    }

    public function index()
    {
        return view('customers.index');
    }

    public function getCustomersData()
    {
        $customers = Customer::query();

        return DataTables::of($customers)
            ->addColumn('action', function ($customer) {
                $actions = '';

                if (auth()->user()->can('customer-edit')) {
                    $actions .= '<a href="' . route('customers.edit', $customer->id) . '" class="btn btn-sm btn-info mx-1">
                        <i class="fas fa-edit"></i> ' . __('messages.edit') . '
                    </a>';
                }

                if (auth()->user()->can('customer-delete')) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-customer" data-id="' . $customer->id . '">
                        <i class="fas fa-trash"></i> ' . __('messages.delete') . '
                    </button>';
                }

                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'nullable|email|max:255|unique:customers',
            'address' => 'nullable|string',
            'id_number' => 'required|string|max:20|unique:customers',
            'id_type' => 'required|in:national_id,iqama,passport',
            'notes' => 'nullable|string'
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_created'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'id_number' => 'required|string|max:20|unique:customers,id_number,' . $customer->id,
            'id_type' => 'required|in:national_id,iqama,passport',
            'notes' => 'nullable|string'
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_updated'));
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return response()->json(['success' => true, 'message' => __('messages.customer_deleted')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('messages.customer_delete_failed')]);
        }
    }
}
