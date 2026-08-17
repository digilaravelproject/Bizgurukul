<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bundle extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'title',
        'slug',
        'description',
        'website_price',
        'affiliate_price',
        'discount_type',
        'discount_value',
        'commission_type',
        'commission_value',
        'commission_amount', // Standard commission for this bundle
        'preference_index', // For Capped Logic
        'final_price',
        'thumbnail',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'website_price' => 'decimal:2',
        'affiliate_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    protected $appends = ['thumbnail_url'];

    protected function thumbnail(): Attribute
    {
        return Attribute::get(function ($value) {
            return $value ? Storage::url($value) : null;
        });
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset($this->thumbnail) : null;
    }

    public function courses()
    {
        return $this->morphedByMany(Course::class, 'item', 'bundle_items')
            ->withPivot('order_column')
            ->orderBy('bundle_items.order_column');
    }

    public function childBundles()
    {
        return $this->morphedByMany(Bundle::class, 'item', 'bundle_items')
            ->withPivot('order_column')
            ->orderBy('bundle_items.order_column');
    }

    public function getAllCoursesFlat()
    {
        $allCourses = $this->courses;
        foreach ($this->childBundles as $child) {
            $allCourses = $allCourses->merge($child->getAllCoursesFlat());
        }

        return $allCourses->unique('id');
    }

    /**
     * Check if the bundle is purchased/unlocked for a user.
     */
    public function isPurchasedBy($userId)
    {
        // Use currently authenticated user to avoid redundant find()
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user || $user->id != $userId) {
            $user = User::find($userId);
        }

        if (! $user) {
            return false;
        }

        return in_array($this->id, $user->unlockedBundleIds());
    }

    /**
     * Scope: Order by preference_index (asc)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('preference_index', 'asc');
    }

    /**
     * Calculate the effective price for a user, handling time-bound upgrades with full tier equity.
     */
    public function getEffectivePriceForUser($user)
    {
        /** @var \App\Models\User $user */
        // Base price depends on whether user was referred
        $basePrice = ($user && $user->referrer) ? (float) $this->affiliate_price : (float) $this->final_price;
        $price = $basePrice;

        if ($user) {
            $maxPref = $user->maxBundlePreferenceIndex();
            // Can only upgrade to higher preference
            if ($maxPref > 0 && $this->preference_index > $maxPref) {
                if ($user->canUpgradeBundles()) {
                    $highestBundle = $user->highestPurchasedBundle();
                    if ($highestBundle) {
                        // 1. Current Tier Full Catalog Value
                        $currentTierValue = ($user->referrer)
                            ? (float) $highestBundle->affiliate_price
                            : (float) $highestBundle->final_price;

                        // 2. Cumulative Lifetime Amount Paid by User across all bundles
                        $totalPaidSoFar = method_exists($user, 'totalBundlePaymentsPaid')
                            ? $user->totalBundlePaymentsPaid()
                            : (float) Payment::where('user_id', $user->id)->whereNotNull('bundle_id')->where('status', 'success')->sum('total_amount');

                        // 3. Deductible credit is the higher of tier value or cumulative amount paid (protects overpayments)
                        $deductibleCredit = max($currentTierValue, $totalPaidSoFar);

                        $diff = $basePrice - $deductibleCredit;
                        $price = max(0, $diff);
                    }
                }
            }
        }

        return $price;
    }

    /**
     * Return the discount amount if upgrading.
     */
    public function getUpgradeDiscountAmount($user)
    {
        /** @var \App\Models\User $user */
        if ($user) {
            $maxPref = $user->maxBundlePreferenceIndex();
            if ($maxPref > 0 && $this->preference_index > $maxPref && $user->canUpgradeBundles()) {
                $highestBundle = $user->highestPurchasedBundle();
                if ($highestBundle) {
                    $currentTierValue = ($user->referrer)
                        ? (float) $highestBundle->affiliate_price
                        : (float) $highestBundle->final_price;

                    $totalPaidSoFar = method_exists($user, 'totalBundlePaymentsPaid')
                        ? $user->totalBundlePaymentsPaid()
                        : (float) Payment::where('user_id', $user->id)->whereNotNull('bundle_id')->where('status', 'success')->sum('total_amount');

                    return max($currentTierValue, $totalPaidSoFar);
                }
            }
        }

        return 0;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
