<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingsHelper
{
    /**
     * Get setting value by key - SQL JSON format অনুযায়ী
     */
    public static function get($key, $default = null)
    {
        try {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            $value = $setting->value;
            
            // যদি value null হয়
            if ($value === null) {
                return $default;
            }
            
            // JSON ডিকোড করার চেষ্টা করুন (SQL এ value গুলো JSON format এ আছে)
            $decoded = json_decode($value, true);
            
            // যদি JSON ডিকোড সফল হয়
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            
            // JSON না হলে, original value রিটার্ন করুন
            return $value;
            
        } catch (\Exception $e) {
            Log::error('SettingsHelper::get error: ' . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Get image URL from settings
     */
    public static function getImageUrl($key, $default = null)
    {
        try {
            $value = self::get($key);
            
            if (!$value) {
                return $default ?? asset('images/default-image.png');
            }
            
            // যদি value array হয় (JSON decoded)
            if (is_array($value)) {
                $path = $value[0] ?? $value['path'] ?? null;
                if (!$path) {
                    return $default ?? asset('images/default-image.png');
                }
                $path = (string) $path;
            } else {
                $path = (string) $value;
            }
            
            // পাথ ক্লিন করুন
            $path = trim($path, '"\'');
            $path = str_replace('\\', '/', $path);
            $path = ltrim($path, '/');
            
            return asset('storage/' . $path);
            
        } catch (\Exception $e) {
            Log::error('SettingsHelper::getImageUrl error: ' . $e->getMessage());
            return $default ?? asset('images/default-image.png');
        }
    }
}