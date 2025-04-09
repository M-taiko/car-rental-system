<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\SparePartSale;
use App\Models\Rental;
use App\Models\Account;
use Carbon\Carbon;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Sales Report (Last 30 Days)
        $sales = SparePartSale::with('sparePart')
            ->where('sale_date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->get();

        $salesChartData = SparePartSale::selectRaw('DATE(sale_date) as date, SUM(total_price) as total')
            ->where('sale_date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        // Spare Parts in Stock
        $spareParts = SparePart::all();

        $sparePartsChartData = SparePart::orderBy('quantity', 'desc')
            ->take(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => $item->quantity];
            });

        // Rentals Report (Last 30 Days)
        $rentals = Rental::with(['user', 'bike'])
            ->where('start_time', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->get();

        $rentalsChartData = Rental::selectRaw('DATE(start_time) as date, COUNT(*) as count')
            ->where('start_time', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Expenses vs Income (Last 30 Days)
        $accounts = Account::where('date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->get();

        $incomeChartData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'income')
            ->where('date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        $expenseChartData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        return view('index', compact(
            'sales',
            'salesChartData',
            'spareParts',
            'sparePartsChartData',
            'rentals',
            'rentalsChartData',
            'accounts',
            'incomeChartData',
            'expenseChartData'
        ));
    }

}
