<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    /**
     * Display system settings page
     */
    public function index()
    {
        $settings = [
            'currency' => BusinessSetting::getSetting('currency', 'USD'),
            'tax_enabled' => BusinessSetting::getSetting('tax_enabled', true),
            'default_tax_rate' => BusinessSetting::getSetting('default_tax_rate', 0),
            'multi_shop' => BusinessSetting::getSetting('multi_shop', true),
            'registration_enabled' => BusinessSetting::getSetting('registration_enabled', true),
            'features' => [
                'pos' => BusinessSetting::getSetting('feature_pos', true),
                'inventory' => BusinessSetting::getSetting('feature_inventory', true),
                'employee_management' => BusinessSetting::getSetting('feature_employee', true),
                'delivery' => BusinessSetting::getSetting('feature_delivery', true),
                'reports' => BusinessSetting::getSetting('feature_reports', true),
            ],
        ];

        return view('system.settings', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|in:USD,KHR,THB',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'tax_enabled' => 'boolean',
            'multi_shop' => 'boolean',
            'registration_enabled' => 'boolean',
        ]);

        // Save settings
        foreach ($validated as $key => $value) {
            BusinessSetting::setSetting($key, $value);
        }

        // Clear settings cache
        Cache::forget('business_settings');

        // Log activity
        ActivityLog::log('update_system_settings', 'system', "Updated system settings");

        return back()->with('alert', [
            'type' => 'success',
            'message' => 'System settings updated successfully!',
        ]);
    }

    /**
     * Toggle feature flag
     */
    public function toggleFeature(Request $request)
    {
        $request->validate([
            'feature' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        $settingKey = 'feature_' . $request->feature;
        BusinessSetting::setSetting($settingKey, $request->enabled);

        // Clear cache
        Cache::forget('business_settings');

        // Log activity
        ActivityLog::log('toggle_feature', 'system', "Toggled feature {$request->feature} to " . ($request->enabled ? 'enabled' : 'disabled'));

        return response()->json([
            'success' => true,
            'message' => "Feature {$request->feature} " . ($request->enabled ? 'enabled' : 'disabled'),
        ]);
    }
}
