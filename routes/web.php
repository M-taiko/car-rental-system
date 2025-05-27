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
    // Dashboard
    Route::get('/index', [HomeController::class, 'index'])->name('index');
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    // Cars
    Route::resource('cars', CarController::class);

    // Drivers
    Route::resource('drivers', DriverController::class);
    Route::put('drivers/{driver}/status', [DriverController::class, 'updateStatus'])->name('drivers.status');

    // Customers
    Route::get('customers/data', [CustomerController::class, 'getCustomersData'])->name('customers.data');
    Route::resource('customers', CustomerController::class);

    // Rentals
    Route::resource('rentals', RentalController::class);
    Route::get('rentals/data', [RentalController::class, 'getRentalsData'])->name('rentals.data');
    Route::post('rentals/store-customer', [RentalController::class, 'storeCustomer'])->name('rentals.storeCustomer');
    Route::post('rentals/store-driver', [RentalController::class, 'storeDriver'])->name('rentals.storeDriver');
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

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

// Language
Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
