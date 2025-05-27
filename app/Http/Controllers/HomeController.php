<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\SparePartSale;
use App\Models\Rental;
use App\Models\Account;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Customer;
use Carbon\Carbon;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Sales Report (Last 30 Days)
        $sales = SparePartSale::with('sparePart')
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
            ->get();

        $salesChartData = SparePartSale::selectRaw('DATE(sale_date) as date, SUM(total_price) as total')
            ->where('sale_date', '>=', Carbon::now()->subDays(30))
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
        $rentals = Rental::with(['customer', 'car'])
            ->where('start_time', '>=', Carbon::now()->subDays(30))
            ->get();

        $rentalsChartData = Rental::selectRaw('DATE(start_time) as date, COUNT(*) as count')
            ->where('start_time', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Expenses vs Income (Last 30 Days)
        $accounts = Account::where('date', '>=', Carbon::now()->subDays(30))
            ->get();

        $incomeChartData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'income')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        $expenseChartData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('date', '>=', Carbon::now()->subDays(30))
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

    public function dashboard()
    {
        // Get recent rentals with relationships
        $rentals = Rental::with(['customer', 'car', 'driver'])
            ->latest()
            ->take(5)
            ->get();

        // Get rentals data for chart (last 30 days)
        $rentalsChartData = Rental::selectRaw('DATE(start_time) as date, COUNT(*) as count')
            ->where('start_time', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Get accounts data
        $accounts = Account::latest()
            ->take(5)
            ->get();

        // Get accounts data for charts (last 30 days)
        $accountsChartData = Account::selectRaw('DATE(date) as date')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('date');

        $incomeData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'income')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        $expenseData = Account::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            });

        return view('dashboard', compact(
            'rentals',
            'rentalsChartData',
            'accounts',
            'accountsChartData',
            'incomeData',
            'expenseData'
        ));
    }
}
