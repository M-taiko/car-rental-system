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
        if ($request->ajax()) {
            $data = Expense::select(['id', 'amount', 'description', 'date']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                ->addColumn('action', function ($row) {
                    $deleteBtn = '<form action="'.route('expenses.destroy', $row->id).'" method="POST" style="display:inline-block;">'
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

        // تسجيل المصروف في الخزينة
        Account::create([
            'type' => 'expense',
            'amount' => $expense->amount,
            'description' => $expense->description ?? trans('messages.general_expense'),
            'date' => $expense->date,
        ]);

        return redirect()->route('expenses.index')->with('success', trans('messages.expense_added_successfully'));
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        // احذف القيد المرتبط من الخزينة
        Account::where('type', 'expense')
               ->where('amount', $expense->amount)
               ->where('description', $expense->description ?? trans('messages.general_expense'))
               ->where('date', $expense->date)
               ->delete();
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', trans('messages.expense_deleted_successfully'));
    }
}
