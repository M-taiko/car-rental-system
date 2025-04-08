<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('Y-m')); // الشهر الحالي افتراضيًا
        $startDate = date('Y-m-01 00:00:00', strtotime($month));
        $endDate = date('Y-m-t 23:59:59', strtotime($month));

        $totalIncome = Account::where('type', 'income')
                             ->whereBetween('date', [$startDate, $endDate])
                             ->sum('amount');
        $totalExpenses = Account::where('type', 'expense')
                               ->whereBetween('date', [$startDate, $endDate])
                               ->sum('amount');
        $balance = $totalIncome - $totalExpenses;

        return view('accounts.index', compact('totalIncome', 'totalExpenses', 'balance', 'month'));
    }

    public function getIncomeData(Request $request)
    {
        if ($request->ajax()) {
            $month = $request->input('month', date('Y-m'));
            $startDate = date('Y-m-01 00:00:00', strtotime($month));
            $endDate = date('Y-m-t 23:59:59', strtotime($month));

            $data = Account::where('type', 'income')
                           ->whereBetween('date', [$startDate, $endDate])
                           ->select(['id', 'type', 'amount', 'description', 'date']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                ->make(true);
        }
    }

    public function getExpensesData(Request $request)
    {
        if ($request->ajax()) {
            $month = $request->input('month', date('Y-m'));
            $startDate = date('Y-m-01 00:00:00', strtotime($month));
            $endDate = date('Y-m-t 23:59:59', strtotime($month));

            $data = Account::where('type', 'expense')
                           ->whereBetween('date', [$startDate, $endDate])
                           ->select(['id', 'type', 'amount', 'description', 'date']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return date('Y-m-d', strtotime($row->date));
                })
                ->make(true);
        }
    }
}
