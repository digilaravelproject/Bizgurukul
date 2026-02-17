<?php

namespace App\Services;

use App\Models\User;
use App\Models\Bundle;
use App\Models\Course; // Assuming products can be courses too? Spec implies "Product Sold" which has preference_index. Usually Bundles have preference_index.
use App\Models\UserAffiliateSetting;
use App\Models\Setting; // Assuming Global Settings model

class CommissionCalculatorService
{
    /**
     * Calculate commission for a given sponsor and product (Bundle).
     *
     * @param User $sponsor
     * @param Bundle $product
     * @return float
     */
    public function calculateCommission(User $sponsor, Bundle $product)
    {
        // 1. Check for Custom Commission Percentage Override
        $sponsorSettings = $sponsor->affiliateSettings;
        if ($sponsorSettings && !is_null($sponsorSettings->custom_commission_percentage)) {
             // Custom Percentage Logic: Commission = Product Price * (Percentage / 100)
             // Using final_price or amount? Assuming final_price or amount user pays.
             // Model has 'final_price' and 'website_price'. Let's use 'final_price' as the base.
             $price = $product->final_price ?? $product->website_price ?? 0;
             return $price * ($sponsorSettings->custom_commission_percentage / 100);
        }

        // 2. Identify Sponsor's Highest Preference Index
        // Fetch sponsor's purchased bundles. Assuming relationship exists: $sponsor->purchasedBundles or activeBundles
        // We need to know how to get sponsor's owned bundles.
        // Assuming $sponsor->ownedBundles() or similar. I'll need to check User model or Repo.
        // For now, I'll assume $sponsor->bundles

        $sponsorBundles = $sponsor->bundles()->where('is_active', true)->get(); // Assuming 'bundles' relation exists for OWNED bundles

        $sponsorHighestBundle = $sponsorBundles->sortByDesc('preference_index')->first();
        $sponsorIndex = $sponsorHighestBundle ? $sponsorHighestBundle->preference_index : 0; // Default to 0 if no bundle

        // 3. Identify Product Index
        $productIndex = $product->preference_index ?? 0;

        // 4. Calculation Logic
        if ($productIndex <= $sponsorIndex) {
            // Scenario A: Full commission of Product Sold
            return $product->commission_amount;
        } else {
            // Scenario B: Capped Commission
            // Commission = commission_amount of the Sponsor’s highest owned bundle
            return $sponsorHighestBundle ? $sponsorHighestBundle->commission_amount : 0;
        }
    }
}
