<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;

class AffiliatePermissionService
{
    /**
     * Check if user can sell courses.
     * Logic: Global Setting -> User Override.
     *
     * @param User $user
     * @return bool
     */
    public function canSellCourses(User $user)
    {
        // 1. Check User Override
        $settings = $user->affiliateSettings;
        if ($settings && !is_null($settings->can_sell_courses)) {
            return $settings->can_sell_courses;
        }

        // 2. Check Global Setting
        // Assuming Setting key 'course_selling_enabled'
        $globalSetting = Setting::where('key', 'course_selling_enabled')->first();
        return $globalSetting ? (bool)$globalSetting->value : false; // Default false if not set? Or true? Spec says "Is Global_Course_Selling ON? If NO..." implies default check.
    }

    /**
     * Check if user is allowed to sell a specific bundle.
     * Logic: Check valid_bundle_ids in User Settings.
     *
     * @param User $user
     * @param int $bundleId
     * @return bool
     */
    public function canSellBundle(User $user, $bundleId)
    {
        $settings = $user->affiliateSettings;

        // If no settings or allowed_bundle_ids is null, allowed all (as per spec "If Null, allowed all")
        if (!$settings || is_null($settings->allowed_bundle_ids)) {
            return true;
        }

        $allowedIds = $settings->allowed_bundle_ids;
        // Ensure it's an array
        if (is_string($allowedIds)) {
             $allowedIds = json_decode($allowedIds, true) ?? [];
        }

        return in_array($bundleId, $allowedIds);
    }
}
