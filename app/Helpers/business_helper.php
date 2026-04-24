<?php

use App\Models\BusinessSetting;

if (!function_exists('business')) {
    /**
     * Get business setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     *
     * Usage:
     * - business('business_name_kh') => "កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត"
     * - business('business_name_en') => "BTEC Cafe & Mini Mart"
     * - business('phone') => "012345678"
     * - business('address') => "Phnom Penh"
     */
    function business(string $key, $default = null)
    {
        return BusinessSetting::get($key, $default);
    }
}

if (!function_exists('business_name')) {
    /**
     * Get business name based on current locale
     *
     * @return string
     */
    function business_name(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'km') {
            return business('business_name_kh', 'កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត');
        }
        return business('business_name_en', 'BTEC Cafe & Mini Mart');
    }
}

if (!function_exists('business_logo')) {
    /**
     * Get business logo URL
     *
     * @return string|null
     */
    function business_logo(): ?string
    {
        $logo = business('logo');
        if ($logo) {
            return asset('storage/' . $logo);
        }
        return null;
    }
}

if (!function_exists('business_info')) {
    /**
     * Get all business info as array
     *
     * @return array
     */
    function business_info(): array
    {
        return BusinessSetting::getAll();
    }
}

if (!function_exists('business_phone')) {
    /**
     * Get business phone number
     *
     * @return string|null
     */
    function business_phone(): ?string
    {
        return business('phone');
    }
}

if (!function_exists('business_address')) {
    /**
     * Get business address
     *
     * @return string|null
     */
    function business_address(): ?string
    {
        return business('address');
    }
}

if (!function_exists('business_email')) {
    /**
     * Get business email
     *
     * @return string|null
     */
    function business_email(): ?string
    {
        return business('email');
    }
}

if (!function_exists('receipt_footer')) {
    /**
     * Get receipt footer text
     *
     * @return string
     */
    function receipt_footer(): string
    {
        return business('receipt_footer_text', 'Thank you for your business!');
    }
}
