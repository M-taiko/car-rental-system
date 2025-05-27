<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'logo',
        'favicon',
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_description',
        'facebook_link',
        'twitter_link',
        'instagram_link',
        'linkedin_link',
        'google_analytics',
        'footer_text',
        'maintenance_mode',
        'maintenance_message'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'maintenance_mode' => 'boolean'
    ];

    public $timestamps = false;

    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return (float) $value;
            case 'json':
            }
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value;
    }

    public static function getLogo()
    {
        $setting = self::where('key', 'logo')->first();
        return $setting ? asset('storage/app/public/profile-photos/' . $setting->value) : asset('assets/img/brand/logo.png');
    }

    public static function getFavicon()
    {
        $setting = self::where('key', 'favicon')->first();
        return $setting ? asset('storage/app/public/profile-photos/' . $setting->value) : asset('assets/img/brand/favicon.png');
    }

    public function getLogoAttribute($value)
    {
        return $value ? asset('storage/app/public/profile-photos/' . $value) : asset('assets/img/brand/logo.png');
    }

    public function getFaviconAttribute($value)
    {
        return $value ? asset('storage/app/public/settings/' . $value) : asset('assets/img/brand/favicon.png');
    }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
