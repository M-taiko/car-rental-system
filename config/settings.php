<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

$settings = [];
try {
    if (Schema::hasTable('settings')) {
        $settings = \App\Models\Setting::all();
    }
} catch (\Exception $e) {
   
}

return [
    'company' => [
        'name' => $settings['company_name'] ?? env('COMPANY_NAME', 'Car Rental System'),
        'logo' => $settings['company_logo'] ?? null,
        'address' => $settings['company_address'] ?? null,
        'phone' => $settings['company_phone'] ?? null,
        'email' => $settings['company_email'] ?? null,
        'website' => $settings['company_website'] ?? null,
        'tax_number' => $settings['tax_number'] ?? null,
        'commercial_number' => $settings['commercial_number'] ?? null,
    ],
    'invoice' => [
        'prefix' => $settings['invoice_prefix'] ?? null,
        'show_tax' => $settings['invoice_show_tax'] ?? null,
        'tax_rate' => $settings['tax_rate'] ?? null,
        'footer_text' => $settings['invoice_footer'] ?? null,
    ],
    'currency' => [
        'symbol' => $settings['currency_symbol'] ?? null,
        'code' => $settings['currency_code'] ?? null,
        'position' => $settings['currency_position'] ?? null,
    ],
];
