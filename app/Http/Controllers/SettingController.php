<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting-list')->only(['index']);
        $this->middleware('permission:setting-edit')->only(['update']);
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_address' => 'required|string',
                'company_phone' => 'required|string|max:20',
                'company_email' => 'required|email',
                'company_website' => 'nullable|url',
                'tax_number' => 'nullable|string',
                'commercial_number' => 'nullable|string',
                'invoice_prefix' => 'nullable|string',
                'tax_rate' => 'required|numeric|min:0|max:100',
                'invoice_footer' => 'nullable|string',
                'currency_symbol' => 'required|string|max:10',
                'currency_code' => 'required|string|max:10',
                'currency_position' => 'required|in:before,after',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                // Get old logo path from database
                $oldLogo = Setting::where('key', 'company_logo')->value('value');

                // Delete old logo if exists
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                // Store new logo in settings directory
                $path = 'settings/' . time() . '_' . $request->file('company_logo')->getClientOriginalName();
                $request->file('company_logo')->storeAs('public', $path);

                // Save logo path in database
                Setting::updateOrCreate(
                    ['key' => 'company_logo'],
                    ['value' => $path]
                );
            }

            // Update other settings
            $settingsToUpdate = [
                'company_name' => $validated['company_name'],
                'company_address' => $validated['company_address'],
                'company_phone' => $validated['company_phone'],
                'company_email' => $validated['company_email'],
                'company_website' => $validated['company_website'] ?? '',
                'tax_number' => $validated['tax_number'] ?? '',
                'commercial_number' => $validated['commercial_number'] ?? '',
                'invoice_prefix' => $validated['invoice_prefix'] ?? 'INV-',
                'tax_rate' => $validated['tax_rate'],
                'invoice_footer' => $validated['invoice_footer'] ?? '',
                'currency_symbol' => $validated['currency_symbol'],
                'currency_code' => $validated['currency_code'],
                'currency_position' => $validated['currency_position'],
            ];

            foreach ($settingsToUpdate as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );

                // Update config
                $configKey = 'settings.' . str_replace('_', '.', $key);
                Config::set($configKey, $value);
            }

            Cache::forget('settings');

            return redirect()->route('settings.index')
                ->with('success', __('messages.settings_updated'));
        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', __('messages.settings_update_failed') . ': ' . $e->getMessage());
        }
    }
}
