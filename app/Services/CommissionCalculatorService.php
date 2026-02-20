<?php

namespace App\Services;

use App\Models\User;
use App\Models\Bundle;
use App\Models\Course; // Assuming products can be courses too? Spec implies "Product Sold" which has preference_index. Usually Bundles have preference_index.
use App\Models\UserAffiliateSetting;
use App\Models\Setting; // Assuming Global Settings model
use Illuminate\Support\Facades\Log;

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
        $productPrice = $product->final_price ?? $product->website_price ?? 0;

        if ($sponsorSettings && !is_null($sponsorSettings->custom_commission_percentage)) {
             return $productPrice * ($sponsorSettings->custom_commission_percentage / 100);
        }

        // 2. Identify Sponsor's Highest Preference Index
        $sponsorBundles = $sponsor->bundles()->where('is_active', true)->get();
        $sponsorHighestBundle = $sponsorBundles->sortByDesc('preference_index')->first();
        $sponsorIndex = $sponsorHighestBundle ? $sponsorHighestBundle->preference_index : 0;

        // 3. Identify Product Index
        $productIndex = $product->preference_index ?? 0;

        // 4. Calculate proper full and capped amounts considering flat/percentage
        $fullCommissionAmount = $product->commission_type === 'percentage'
            ? $productPrice * ($product->commission_value / 100)
            : $product->commission_value;

        Log::info("Commission Calc: Sponsor [{$sponsor->id}] Index: $sponsorIndex | Product [{$product->id}] Index: $productIndex");

        if ($productIndex <= $sponsorIndex) {
            // Full commission
            return $fullCommissionAmount ?? 0;
        } else {
            // Capped Commission
            if ($sponsorHighestBundle) {
                $cappedAmount = $sponsorHighestBundle->commission_type === 'percentage'
                    ? $productPrice * ($sponsorHighestBundle->commission_value / 100)
                    : $sponsorHighestBundle->commission_value;
                return $cappedAmount ?? 0;
            }
            return 0;
        }
    }
}
