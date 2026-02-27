<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:manage-settings');
    // }

    public function index(Request $request)
    {
        $group = $request->get('group', 'general');
        
        $validGroups = Setting::distinct()->pluck('group')->toArray();
        if (!in_array($group, $validGroups) && !empty($validGroups)) {
            $group = $validGroups[0]; 
        }
        
        $settings = Setting::where('group', $group)
            ->where('is_visible', true)
            ->orderBy('id')
            ->get();

        $groups = Setting::GROUPS;
        
        return view('admin.settings.index', compact('settings', 'group', 'groups'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting && $setting->is_editable) {
                // Handle file uploads
                if ($request->hasFile("settings.{$key}")) {
                    $value = $this->uploadSettingFile($request->file("settings.{$key}"), $key);
                }
                
                $setting->value = $value;
                $setting->save();
            }
        }

        // Clear cache
        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function create()
    {
        $groups = Setting::GROUPS;
        $types = Setting::TYPES;
        
        return view('admin.settings.create', compact('groups', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'label' => 'required|string|max:255',
            'group' => 'required|string',
            'type' => 'required|string',
            'value' => 'nullable',
            'description' => 'nullable|string',
            'options' => 'nullable|array'
        ]);

        Setting::create([
            'key' => $request->key,
            'label' => $request->label,
            'group' => $request->group,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description,
            'options' => $request->options,
            'is_editable' => true,
            'is_visible' => true
        ]);

        Cache::forget('app_settings');

        return redirect()->route('admin.settings.index', ['group' => $request->group])
            ->with('success', 'Setting created successfully.');
    }

    public function edit(Setting $setting)
    {
        $groups = Setting::GROUPS;
        $types = Setting::TYPES;
        
        return view('admin.settings.edit', compact('setting', 'groups', 'types'));
    }

    public function updateSetting(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key,' . $setting->id,
            'label' => 'required|string|max:255',
            'group' => 'required|string',
            'type' => 'required|string',
            'value' => 'nullable',
            'description' => 'nullable|string',
            'options' => 'nullable|array'
        ]);

        $setting->update($request->all());
        
        Cache::forget('app_settings');

        return redirect()->route('admin.settings.index', ['group' => $setting->group])
            ->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        if (!$setting->is_editable) {
            return redirect()->back()->with('error', 'This setting cannot be deleted.');
        }

        $setting->delete();
        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Setting deleted successfully.');
    }

    public function maintenance(Request $request)
    {
        if ($request->has('enable')) {
            Artisan::call('down', [
                '--retry' => $request->retry ?? 60,
                '--secret' => $request->secret ?? Str::random(32)
            ]);
            $message = 'Maintenance mode enabled.';
        } else {
            Artisan::call('up');
            $message = 'Maintenance mode disabled.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function cache()
    {
        Artisan::call('optimize:clear');
        
        return redirect()->back()->with('success', 'All cache cleared successfully.');
    }

    public function backup()
    {
        try {
            Artisan::call('backup:run');
            return redirect()->back()->with('success', 'Backup created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    protected function uploadSettingFile($file, $key)
    {
        $path = 'settings/' . date('Y/m');
        $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        return $file->storeAs($path, $filename, 'public');
    }
}