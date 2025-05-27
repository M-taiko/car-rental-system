<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

$settings = [];
try {
    if (Schema::hasTable('settings')) {
        $settings = Setting::pluck('value', 'key')->toArray();
    }
} catch (\Exception $e) {
    // Table might not exist during fresh installation
}

return [
    'company' => [
        'name' => $settings['company_name'] ?? env('COMPANY_NAME', 'Car Rental System'),
        'logo' => $settings['company_logo'] ?? null,
        'address' => $settings['company_address'] ?? env('COMPANY_ADDRESS', 'Your Company Address'),
        'phone' => $settings['company_phone'] ?? env('COMPANY_PHONE', '+1234567890'),
        'email' => $settings['company_email'] ?? env('COMPANY_EMAIL', 'info@company.com'),
        'website' => $settings['company_website'] ?? env('COMPANY_WEBSITE', 'www.company.com'),
        'tax_number' => $settings['tax_number'] ?? env('COMPANY_TAX_NUMBER', '123456789'),
        'commercial_number' => $settings['commercial_number'] ?? env('COMPANY_COMMERCIAL_NUMBER', '987654321'),
    ],
    'invoice' => [
        'prefix' => $settings['invoice_prefix'] ?? env('INVOICE_PREFIX', 'INV-'),
        'show_tax' => env('INVOICE_SHOW_TAX', true),
        'tax_rate' => $settings['tax_rate'] ?? env('INVOICE_TAX_RATE', 15),
        'footer_text' => $settings['invoice_footer'] ?? env('INVOICE_FOOTER_TEXT', 'Thank you for your business!'),
    ],
    'currency' => [
        'symbol' => $settings['currency_symbol'] ?? env('CURRENCY_SYMBOL', '$'),
        'code' => $settings['currency_code'] ?? env('CURRENCY_CODE', 'USD'),
        'position' => $settings['currency_position'] ?? env('CURRENCY_POSITION', 'before'),
    ],
];
