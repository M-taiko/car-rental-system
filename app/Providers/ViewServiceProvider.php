<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\RentalController;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // No view composers needed
    }

    public function register()
    {
        //
    }
}
