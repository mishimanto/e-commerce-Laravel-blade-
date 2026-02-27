<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key', 'value', 'group', 'type', 'label', 'description',
        'options', 'is_editable', 'is_visible'
    ];

    protected $casts = [
        'value' => 'json',
        'options' => 'json',
        'is_editable' => 'boolean',
        'is_visible' => 'boolean'
    ];

    const TYPES = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'number' => 'Number',
        'email' => 'Email',
        'url' => 'URL',
        'password' => 'Password',
        'select' => 'Dropdown',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio',
        'file' => 'File Upload',
        'image' => 'Image',
        'color' => 'Color Picker',
        'date' => 'Date',
        'time' => 'Time',
        'datetime' => 'Date & Time',
        'editor' => 'Rich Text Editor',
        'code' => 'Code Editor'
    ];

    const GROUPS = [
        'general' => 'General Settings',
        'store' => 'Store Information',
        'payment' => 'Payment Settings',
        'shipping' => 'Shipping Settings',
        'courier' => 'Courier Settings',
        'email' => 'Email Settings',
        'sms' => 'SMS Settings',
        'social' => 'Social Media',
        'seo' => 'SEO Settings',
        'theme' => 'Theme Settings',
        'customization' => 'Customization',
        'api' => 'API Settings',
        'security' => 'Security Settings',
        'notification' => 'Notification Settings',
        'invoice' => 'Invoice Settings',
        'tax' => 'Tax Settings',
        'currency' => 'Currency Settings',
        'language' => 'Language Settings',
        'maintenance' => 'Maintenance Mode',
        'backup' => 'Backup Settings'
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('app_settings');
        });

        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        try {
            $settings = Cache::remember('app_settings', 86400, function () {
                return self::pluck('value', 'key')->toArray();
            });

            $value = $settings[$key] ?? $default;

            // Decode JSON value - use self:: instead of $this->
            if (is_string($value) && self::isJson($value)) {
                return json_decode($value, true);
            }

            return $value;
            
        } catch (\Exception $e) {
            // If cache fails, try direct database query
            try {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            } catch (\Exception $ex) {
                return $default;
            }
        }
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'group' => 'custom',
                'type' => 'text',
                'label' => ucfirst(str_replace('_', ' ', $key))
            ]);
        }

        Cache::forget('app_settings');
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup($group)
    {
        return self::where('group', $group)
                   ->where('is_visible', true)
                   ->orderBy('id')
                   ->get();
    }

    /**
     * Check if string is valid JSON (static method)
     */
    private static function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('app_settings');
    }

    /**
     * Refresh settings cache
     */
    public static function refreshCache()
    {
        Cache::forget('app_settings');
        return self::pluck('value', 'key')->toArray();
    }
}