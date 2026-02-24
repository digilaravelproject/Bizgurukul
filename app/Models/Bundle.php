<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class Bundle extends Model
{
    use HasFactory;

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
        'is_active'
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
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find($userId);
        if (!$user) {
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
     * Calculate the effective price for a user, handling time-bound upgrades.
     */
    public function getEffectivePriceForUser($user)
    {
        $price = $this->final_price;

        if ($user) {
            $maxPref = $user->maxBundlePreferenceIndex();
            // Can only upgrade to higher preference
            if ($maxPref > 0 && $this->preference_index > $maxPref) {
                if ($user->canUpgradeBundles()) {
                    $highestBundle = $user->highestPurchasedBundle();
                    if ($highestBundle) {
                        $diff = $this->final_price - $highestBundle->final_price;
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
        if ($user) {
            $maxPref = $user->maxBundlePreferenceIndex();
            if ($maxPref > 0 && $this->preference_index > $maxPref && $user->canUpgradeBundles()) {
                $highestBundle = $user->highestPurchasedBundle();
                if ($highestBundle) {
                    return $highestBundle->final_price; // Current value as discount
                }
            }
        }
        return 0;
    }
}

