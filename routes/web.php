<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\SparePartSaleController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\CarTypeController;
use App\Http\Controllers\ThirdPartyCarController;
use App\Http\Controllers\ReportController;

Auth::routes();

Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::group(['middleware' => ['auth']], function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');

    
    // Dashboard
    Route::get('/index', [HomeController::class, 'index'])->name('index');
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    // Cars
    Route::resource('cars', CarController::class);

    // Driver
    Route::resource('driver', DriverController::class);
    Route::put('driver/{driver}/status', [DriverController::class, 'updateStatus'])->name('driver.status');

    // Customers
    Route::get('customers/data', [CustomerController::class, 'getCustomersData'])->name('customers.data');
    Route::resource('customers', CustomerController::class);

    // Rentals
    Route::get('rentals/data', [RentalController::class, 'getRentalsData'])->name('rentals.getRentalsData');
    Route::get('rentals/return/form', [RentalController::class, 'showReturnForm'])->name('rentals.showReturnForm');
    Route::get('rentals/return/details', [RentalController::class, 'calculateReturnDetails'])->name('rentals.calculateReturnDetails');

    Route::resource('rentals', RentalController::class);
    
    // Routes
    Route::get('routes/select', [RouteController::class, 'getRoutesForSelect'])->name('routes.select');

    // Settings
    Route::get('setting', [SettingController::class, 'index'])->name('settings.index');
    Route::put('setting', [SettingController::class, 'update'])->name('settings.update');

    Route::post('rentals/store-customer', [RentalController::class, 'storeCustomer'])->name('rentals.storeCustomer');
    Route::post('rentals/store-driver', [RentalController::class, 'storeDriver'])->name('rentals.storeDriver');
    Route::post('rentals/store-route', [RentalController::class, 'storeRoute'])->name('rentals.storeRoute');
    Route::post('rentals/{rental}/return', [RentalController::class, 'returnCar'])->name('rentals.return');
    Route::post('rentals/{rental}/calculate-cost', [RentalController::class, 'calculateCost'])->name('rentals.calculateCost');
    Route::get('rentals/{rental}/invoice', [RentalController::class, 'getInvoice'])->name('rentals.invoice');
    Route::get('rentals/{rental}/show-invoice', [RentalController::class, 'showInvoice'])->name('rentals.showInvoice');

    // Maintenance
    Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/data', [MaintenanceController::class, 'getMaintenanceData'])->name('maintenance.data');
    Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::post('maintenance/store-customer', [MaintenanceController::class, 'storeCustomer'])->name('maintenance.storeCustomer');
    Route::post('maintenance/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    Route::delete('maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('maintenance/{id}/invoice', [MaintenanceController::class, 'getInvoice'])->name('maintenance.invoice');
    Route::get('maintenance/spare-parts-profit-report', [MaintenanceController::class, 'sparePartsProfitReport'])
        ->name('maintenance.spare_parts_profit_report');

    // Spare Parts
    Route::resource('spare-parts', SparePartController::class);
    Route::get('spare-parts-data', [SparePartController::class, 'getSparePartsData'])->name('spare-parts.data');

    // Spare Part Sales
    Route::resource('spare-part-sales', SparePartSaleController::class);
    Route::get('spare-part-sales-data', [SparePartSaleController::class, 'getSparePartSalesData'])->name('spare-part-sales.data');


    // Accounts
    Route::resource('accounts', AccountController::class)->except(['show']);
    Route::get('accounts-data', [AccountController::class, 'getAccountsData'])->name('accounts.data');
    Route::get('accounts/income', [AccountController::class, 'income'])->name('accounts.income');
    Route::get('accounts/expenses', [AccountController::class, 'expenses'])->name('accounts.expenses');
    Route::get('accounts/totals', [AccountController::class, 'totals'])->name('accounts.totals');
    Route::post('accounts/store-income', [AccountController::class, 'storeIncome'])->name('accounts.storeIncome');
    Route::post('accounts/store-expense', [AccountController::class, 'storeExpense'])->name('accounts.storeExpense');

    // Users & Roles
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    // Routes Management
    Route::resource('routes', RouteController::class)->except(['show']);
    Route::put('routes/{route}/toggle-status', [RouteController::class, 'toggleStatus'])->name('routes.toggle-status');
    
    // Work Shifts
    Route::resource('work-shifts', WorkShiftController::class)->except(['show']);
    
    // Car Types
    Route::resource('car-types', CarTypeController::class)->except(['show']);
    
    // Third Party Cars
    Route::resource('third-party-cars', ThirdPartyCarController::class);
    Route::post('third-party-cars/{third_party_car}/approve', [ThirdPartyCarController::class, 'approve'])->name('third-party-cars.approve');
    Route::post('third-party-cars/{third_party_car}/reject', [ThirdPartyCarController::class, 'reject'])->name('third-party-cars.reject');
    Route::post('third-party-cars/{third_party_car}/complete', [ThirdPartyCarController::class, 'complete'])->name('third-party-cars.complete');

    // Debug route to check user permissions and roles
    Route::get('/debug/permissions', function() {
        $user = auth()->user();
        
        if (!$user) {
            return 'No authenticated user';
        }
        
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'can_view_rentals' => $user->can('report-view-rentals'),
            'can_export' => $user->can('report-export')
        ];
    })->middleware('auth');

    // Reports
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
        // Main Reports Dashboard
        Route::get('/', [ReportController::class, 'index'])
            ->middleware('check.permission:report-view-rentals')
            ->name('index');
        
        // Rental Reports
        Route::get('rentals', [ReportController::class, 'rentalReport'])
            ->middleware('check.permission:report-view-rentals')
            ->name('rentals');
            
        // Rental Reports Data (for DataTables)
        Route::get('rentals/data', [ReportController::class, 'getRentalData'])
            ->middleware('check.permission:report-view-rentals')
            ->name('rentals.data');
        
        // Third Party Cars Reports
        Route::get('third-party-cars', [ReportController::class, 'thirdPartyCars'])
            ->middleware('check.permission:report-view-third-party-cars')
            ->name('third-party-cars');
        
        // Car Types Reports
        Route::get('car-types', [ReportController::class, 'carTypes'])
            ->middleware('check.permission:report-view-car-types')
            ->name('car-types');
        
        // Monthly Revenue Reports
        Route::get('monthly-revenue', [ReportController::class, 'monthlyRevenue'])
            ->middleware('check.permission:report-view-monthly-revenue')
            ->name('monthly-revenue');
        
        // Export Reports
        Route::get('export/{type}/{format}', [ReportController::class, 'export'])
            ->where('type', 'rentals|third-party-cars|car-types|monthly-revenue')
            ->where('format', 'excel|pdf')
            ->middleware('check.permission:report-export')
            ->name('export');
    });

    // Settings
    Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
});

// Language
Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
