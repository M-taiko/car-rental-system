<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses.index');
    }

    public function getExpensesData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
        }

        try {
            $data = Expense::select(['id', 'amount', 'description', 'date']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '';
                    $actionBtn .= '<a href="' . route('expenses.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> ' . trans('messages.show') . '</a> ';
                    $actionBtn .= '<a href="javascript:void(0)" class="btn btn-sm btn-primary editExpense" data-id="' . $row->id . '"><i class="fas fa-edit"></i> ' . trans('messages.edit') . '</a> ';

                    if (auth()->user()->hasPermissionTo('delete-expenses')) {
                        $actionBtn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger deleteExpense" data-id="' . $row->id . '"><i class="fas fa-trash"></i> ' . trans('messages.delete') . '</a>';
                    }
                    return $actionBtn ?: 'No Actions';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        Account::create([
            'type' => 'expense',
            'amount' => $expense->amount,
            'description' => $expense->description ?? trans('messages.general_expense'),
            'date' => $expense->date,
            // مش هنستخدم expense_id لأنك بتعتمد على type
        ]);

        return redirect()->route('expenses.index')->with('success', trans('messages.expense_added_successfully'));
    }

    public function show($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', trans('messages.expense_not_found'));
        }
        // جلب القيود بناءً على type = 'expense' ومطابقة التاريخ والمبلغ
        $account = Account::where('type', 'expense')
                         ->where('amount', $expense->amount)
                         ->where('date', $expense->date)
                         ->first();
        return view('expenses.show', compact('expense', 'account'));
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // جلب القيد القديم بناءً على type والبيانات القديمة
        $account = Account::where('type', 'expense')
                         ->where('amount', $expense->amount)
                         ->where('date', $expense->date)
                         ->first();

        $expense->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        // تحديث القيد
        if ($account) {
            $account->update([
                'amount' => $expense->amount,
                'description' => $expense->description ?? trans('messages.general_expense'),
                'date' => $expense->date,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => trans('messages.expense_updated_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.expense_not_found'),
            ], 404);
        }

        // حذف القيد بناءً على type والبيانات
        Account::where('type', 'expense')
               ->where('amount', $expense->amount)
               ->where('date', $expense->date)
               ->delete();

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => trans('messages.expense_deleted_successfully'),
        ]);
    }
}
