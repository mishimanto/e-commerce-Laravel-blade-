<?php
// app/Providers/SettingsServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $settings = Cache::remember('app_settings', 86400, function () {
                return Setting::all()->keyBy('key')->map(function ($setting) {
                    $value = $setting->value;

                    return $value;
                })->toArray();
            });

            config()->set('settings', $settings);
            
        } catch (\Exception $e) {
            config()->set('settings', []);
        }
    }
}