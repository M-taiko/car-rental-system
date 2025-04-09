<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('Y-m')); // استخدمنا date() بدل now()
        $totalIncome = Account::where('type', 'income')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
        $totalExpenses = Account::where('type', 'expense')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
        $balance = $totalIncome - $totalExpenses;

        return view('accounts.index', compact('month', 'totalIncome', 'totalExpenses', 'balance'));
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return view('accounts.show', compact('account'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $account = Account::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.account_added_successfully'),
            'account' => $account,
        ]);
    }

    public function income(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $query = Account::where('type', 'income')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2));

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('type', function ($account) {
                return $account->type == 'income' ? __('messages.income') : __('messages.expense');
            })
            ->editColumn('date', function ($account) {
                // استخدام strtotime() و date() لتنسيق التاريخ
                return date('Y-m-d H:i', strtotime($account->date));
            })
            ->make(true);
    }

    public function expenses(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $query = Account::where('type', 'expense')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2));

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('type', function ($account) {
                return $account->type == 'income' ? __('messages.income') : __('messages.expense');
            })
            ->editColumn('date', function ($account) {
                // استخدام strtotime() و date() لتنسيق التاريخ
                return date('Y-m-d H:i', strtotime($account->date));
            })
            ->make(true);
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.account_deleted_successfully'),
        ]);
    }

    public function totals(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $totalIncome = Account::where('type', 'income')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
        $totalExpenses = Account::where('type', 'expense')
            ->whereYear('date', '=', substr($month, 0, 4))
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->sum('amount');
        $balance = $totalIncome - $totalExpenses;

        return response()->json([
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'balance' => $balance,
        ]);
    }
}
