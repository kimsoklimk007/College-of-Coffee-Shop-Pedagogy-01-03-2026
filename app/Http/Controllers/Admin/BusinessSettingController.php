<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    /**
     * Display business settings page
     */
    public function index()
    {
        $setting = BusinessSetting::first();

        if (!$setting) {
            $setting = BusinessSetting::create(BusinessSetting::getDefaults());
        }

        return view('admin.profile.business', compact('setting'));
    }

    /**
     * Update business settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name_kh' => 'required|string|max:255',
            'business_name_en' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:100',
            'receipt_footer_text' => 'nullable|string|max:500',
            'currency_code' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $setting = BusinessSetting::first();

        if (!$setting) {
            $setting = new BusinessSetting();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo && Storage::exists('public/' . $setting->logo)) {
                Storage::delete('public/' . $setting->logo);
            }

            $logoPath = $request->file('logo')->store('business', 'public');
            $validated['logo'] = $logoPath;
        }

        $setting->fill($validated);
        $setting->save();

        // Clear cache
        BusinessSetting::clearCache();

        return redirect()->route('business.settings')->with('alert', [
            'type' => 'success',
            'message' => __('Business settings updated successfully!'),
        ]);
    }

    /**
     * Delete logo
     */
    public function deleteLogo()
    {
        $setting = BusinessSetting::first();

        if ($setting && $setting->logo) {
            if (Storage::exists('public/' . $setting->logo)) {
                Storage::delete('public/' . $setting->logo);
            }

            $setting->logo = null;
            $setting->save();

            BusinessSetting::clearCache();
        }

        return redirect()->route('business.settings')->with('alert', [
            'type' => 'success',
            'message' => __('Logo deleted successfully!'),
        ]);
    }

    /**
     * API: Get business info
     */
    public function getBusinessInfo()
    {
        $setting = BusinessSetting::first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Business settings not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting->toArray(),
        ]);
    }
}
