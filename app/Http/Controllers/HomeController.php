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
        // العدادات
        $activeRentals = Rental::where('status', 'active')->count();
        $availableCars = Rental::whereHas('car', fn($q) => $q->where('status', 'available'))->count();
        $totalCustomers = Customer::count();
        $totalDrivers = Driver::count();
        $availableDrivers = Driver::where('status', 'available')->count();

        // آخر 5 إيجارات
        $recentRentals = Rental::with(['car', 'customer'])->latest()->take(5)->get();

        // آخر 5 حسابات
        $recentAccounts = Account::latest()->take(5)->get();

        // إجمالي الإيرادات والمصروفات
        $totalRevenue = Account::where('type', 'income')->sum('amount');
        $totalExpenses = Account::where('type', 'expense')->sum('amount');


        $newCustomers = Customer::where('created_at', '>=', now()->subDays(30))->count();

        // بيانات الرسم البياني (آخر 30 يومًا)
        $dates = [];
        $incomeData = [];
        $expenseData = [];
        $rentalCounts = [];

        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays(29 - $i)->format('Y-m-d');
            $dates[] = $date;
            $incomeData[] = Account::where('type', 'income')->whereDate('date', $date)->sum('amount') ?? 0;
            $expenseData[] = Account::where('type', 'expense')->whereDate('date', $date)->sum('amount') ?? 0;
            $rentalCounts[] = Rental::whereDate('start_time', $date)->count();
        }

        return view('dashboard', compact(
            'activeRentals',
            'availableCars',
            'totalCustomers',
            'totalDrivers',
            'availableDrivers',
            'recentRentals',
            'recentAccounts',
            'totalRevenue',
            'totalExpenses',
            'newCustomers',
            'dates',
            'incomeData',
            'expenseData',
            'rentalCounts'
        ));
    }
}


