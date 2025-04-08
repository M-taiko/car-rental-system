<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    public function handle(Request $request, Closure $next)
{
    $locale = session()->get('locale', 'en');
    file_put_contents(storage_path('logs/laravel.log'), "Locale: $locale\n", FILE_APPEND); // Direct file write
    App::setLocale($locale);
    return $next($request);
}

}
