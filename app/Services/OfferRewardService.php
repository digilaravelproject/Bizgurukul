<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Collection;

class OfferRewardService
{
    /**
     * Compute total reward amount eligible for standard calculation inclusion.
     * Excludes active phase offers (start_date <= now() <= end_date);
     * Automatically includes expired phase offers (now() > end_date).
     */
    public function getEligibleOfferRewardsForUser(User $user): float
    {
        $expiredOffers = Offer::expiredPhase()->get();

        $eligibleTotal = 0.0;
        foreach ($expiredOffers as $offer) {
            // If target_amount is set, verify if user met target earnings
            if ($offer->target_amount > 0) {
                $userEarnings = $user->getEarningsInRange($offer->start_date, $offer->end_date);
                if ($userEarnings >= $offer->target_amount) {
                    $eligibleTotal += $offer->reward_value;
                }
            } else {
                $eligibleTotal += $offer->reward_value;
            }
        }

        return (float) $eligibleTotal;
    }

    /**
     * Get active phase offers currently EXCLUDED from standard calculations.
     */
    public function getExcludedActiveOffers(): Collection
    {
        return Offer::activePhase()->get();
    }

    /**
     * Get expired phase offers automatically INCLUDED in calculations.
     */
    public function getIncludedExpiredOffers(): Collection
    {
        return Offer::expiredPhase()->get();
    }
}
