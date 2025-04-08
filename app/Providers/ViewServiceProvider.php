<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\RentalController;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $rentalController = app(RentalController::class);
            $notifications = $rentalController->getNotifications();
            $view->with('notifications', $notifications);
        });
    }

    public function register()
    {
        //
    }
}
