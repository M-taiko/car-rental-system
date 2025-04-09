<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;


use App\Http\Controllers\BikeController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\SparePartSaleController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MaintenanceController;




use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;




Auth::routes();

Route::get('/', function () { return redirect('/login'); })->name('home');

Route::get('expenses/data', [ExpenseController::class, 'getExpensesData'])->name('expenses.data'); //بشكل مؤقت طبعا


Route::group(['middleware' => ['auth']], function () {

    Route::get('/index', [HomeController::class, 'index'])->name('index');

    Route::resource('bikes', BikeController::class);
    Route::get('bikes-data', [BikeController::class, 'getBikesData'])->name('bikes.data');



    Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('rentals/data', [RentalController::class, 'getRentalsData'])->name('rentals.data');
    Route::post('rentals/store-customer', [RentalController::class, 'storeCustomer'])->name('rentals.storeCustomer');
    Route::post('rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::post('rentals/{id}/return', [RentalController::class, 'returnBike'])->name('rentals.return');
    Route::post('rentals/{id}/calculate-cost', [RentalController::class, 'calculateCost'])->name('rentals.calculateCost');
    Route::get('rentals/{id}/invoice', [RentalController::class, 'getInvoice'])->name('rentals.getInvoice');
    Route::get('rentals/{id}/show-invoice', [RentalController::class, 'showInvoice'])->name('rentals.showInvoice');
    Route::delete('rentals/{id}', [RentalController::class, 'destroy'])->name('rentals.destroy');


 // Routes للصيانة
    Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/data', [MaintenanceController::class, 'getMaintenanceData'])->name('maintenance.data');
    Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store'); // تعديل هنا
    Route::post('maintenance/store-customer', [MaintenanceController::class, 'storeCustomer'])->name('maintenance.storeCustomer');
    Route::post('maintenance/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    Route::delete('maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('maintenance/{id}/invoice', [MaintenanceController::class, 'getInvoice'])->name('maintenance.invoice');

    Route::get('/maintenance/spare-parts-profit-report', [MaintenanceController::class, 'sparePartsProfitReport'])->name('maintenance.spare_parts_profit_report');

    // Routes للمصروفات

    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');



    // Routes للخزينة

    Route::resource('accounts', AccountController::class)->except(['show']); // تعطيل الـ Route show
    Route::get('accounts/income', [AccountController::class, 'income'])->name('accounts.income');
    Route::get('accounts/expenses', [AccountController::class, 'expenses'])->name('accounts.expenses');
    Route::get('accounts/totals', [AccountController::class, 'totals'])->name('accounts.totals');


    Route::resource('spare-parts', SparePartController::class);
    Route::get('spare-parts-data', [SparePartController::class, 'getSparePartsData'])->name('spare-parts.data');

    Route::resource('spare-part-sales', SparePartSaleController::class);
    Route::get('spare-part-sales-data', [SparePartSaleController::class, 'getSparePartSalesData'])->name('spare-part-sales.data');



Route::resource('accounts', AccountController::class);
Route::get('accounts-data', [AccountController::class, 'getAccountsData'])->name('accounts.data');



    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});




Route::get('/switch-language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');





Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login'); // Redirect user to the login page
})->name('logout');

// Place this catch-all route at the end to avoid conflicts
// Route::get('/{page}', [AdminController::class, 'index'])->where('page', '.*');
