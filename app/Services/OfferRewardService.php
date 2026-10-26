<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Collection;

class OfferRewardService
{
    /**
     * Get offers whose date windows are excluded from standard reward calculations.
     */
    public function getOffersExcludingRanges(): Collection
    {
        return Offer::where('is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('start_date')
                  ->orWhereNotNull('end_date');
            })
            ->get();
    }

    /**
     * Get user earnings strictly within an offer's active time frame.
     */
    public function getOfferEarnings(User $user, Offer $offer): float
    {
        return $user->getEarningsInRange($offer->start_date, $offer->end_date, false);
    }

    /**
     * Calculate total commission earnings excluded from standard rewards due to promotional offers.
     */
    public function getExcludedEarningsTotalForUser(User $user, $startDate = null, $endDate = null): float
    {
        $all = $user->getEarningsInRange($startDate, $endDate, false);
        $rewardEligible = $user->getEarningsInRange($startDate, $endDate, true);

        return max(0, $all - $rewardEligible);
    }

    /**
     * Compute total reward amount eligible for standard calculation inclusion.
     */
    public function getEligibleOfferRewardsForUser(User $user): float
    {
        $expiredOffers = Offer::expiredPhase()->get();

        $eligibleTotal = 0.0;
        foreach ($expiredOffers as $offer) {
            if ($offer->target_amount > 0) {
                $userEarnings = $this->getOfferEarnings($user, $offer);
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
     * Get expired phase offers.
     */
    public function getIncludedExpiredOffers(): Collection
    {
        return Offer::expiredPhase()->get();
    }
}
