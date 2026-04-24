<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name_kh',
        'business_name_en',
        'phone',
        'address',
        'email',
        'website',
        'logo',
        'tax_number',
        'receipt_footer_text',
        'currency_code',
        'exchange_rate',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'exchange_rate' => 'decimal:2',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember('business_setting_' . $key, 3600, function () use ($key, $default) {
            $setting = self::first();
            if (!$setting) {
                return $default;
            }
            return $setting->$key ?? $default;
        });
    }

    /**
     * Get all settings as an array
     *
     * @return array
     */
    public static function getAll(): array
    {
        return Cache::remember('business_settings_all', 3600, function () {
            $setting = self::first();
            if (!$setting) {
                return self::getDefaults();
            }
            return $setting->toArray();
        });
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('business_settings_all');
        $setting = self::first();
        if ($setting) {
            foreach ($setting->getFillable() as $key) {
                Cache::forget('business_setting_' . $key);
            }
        }
    }

    /**
     * Get default values for settings
     *
     * @return array
     */
    public static function getDefaults(): array
    {
        return [
            'business_name_kh' => 'កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត',
            'business_name_en' => 'BTEC Cafe & Mini Mart',
            'phone' => '012345678',
            'address' => 'Phnom Penh, Cambodia',
            'email' => 'info@bteccafe.com',
            'website' => '',
            'logo' => null,
            'tax_number' => '',
            'receipt_footer_text' => 'Thank you for your business!',
            'currency_code' => 'KHR',
            'exchange_rate' => 4100,
            'is_active' => true,
        ];
    }

    /**
     * Boot the model
     */
    /**
     * Get a setting value (alias for get method)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting(string $key, $default = null)
    {
        return self::get($key, $default);
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function setSetting(string $key, $value): bool
    {
        $setting = self::first();

        if (!$setting) {
            $setting = self::create([
                $key => $value,
            ]);
            return true;
        }

        $setting->$key = $value;
        $setting->save();

        return true;
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
