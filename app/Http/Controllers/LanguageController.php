<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    // public function switch(Request $request)
    // {
    //     $locale = $request->input('locale');
    //     if (in_array($locale, ['en', 'ar', 'zh'])) {
    //         session()->put('locale', $locale);
    //     }
    //     return redirect()->back();
    // }
    public function switch(Request $request, $locale)
    {
        // Log the incoming locale
        \Log::info('Switch requested with locale: ' . $locale);

        // Check if locale is valid
        if (in_array($locale, ['en', 'ar', 'zh', 'hi'])) {
            session()->put('locale', $locale);
            \Log::info('Session locale set to: ' . session()->get('locale'));
        } else {
            // Optionally: fallback to English if invalid
            session()->put('locale', 'en');
            \Log::info('Invalid locale: ' . $locale);
        }

        return redirect()->back();
    }
}
