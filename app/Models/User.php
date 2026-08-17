<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Models\Bundle;
use App\Models\Payment;
use App\Models\AffiliateCommission;
use App\Models\KycDetail;
use App\Models\BankDetail;
use App\Models\State;
use App\Models\BankUpdateRequest;
use App\Models\WalletTransaction;
use App\Models\ReferralVisit;
use App\Models\CommissionRule;
use App\Models\UserAffiliateSetting;
use App\Models\Setting;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\BeginnerGuideView;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /* =========================================================================
       1. MODEL CONFIGURATION & ATTRIBUTES
       ========================================================================= */

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'gender',
        'dob',
        'state_id',
        'zip_code',
        'address',
        'profile_picture',
        'is_active',
        'referral_code',
        'referred_by',
        'kyc_status',
        'is_banned',
        'banned_at',
        'hide_from_leaderboard',
        'profile_photo_url',
        'deleted_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'survey_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'dob' => 'date:Y-m-d',
        'kyc_status' => 'string',
        'is_banned' => 'boolean',
        'hide_from_leaderboard' => 'boolean',
        'banned_at' => 'datetime',
        'deleted_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'survey_completed' => 'boolean',
    ];

    /* =========================================================================
       2. BOOT & EVENT HOOKS
       ========================================================================= */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode($user->name ?? 'USER');
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['password', 'remember_token']);
    }

    private static function generateUniqueReferralCode($name): string
    {
        $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        if (empty($cleanName)) {
            $cleanName = 'USER';
        }
        $prefixLength = strlen($cleanName) > 4 ? 5 : strlen($cleanName);
        $namePart = substr($cleanName, 0, $prefixLength);
        $randomLength = 8 - strlen($namePart);
        do {
            $numberPart = '';
            for ($i = 0; $i < $randomLength; $i++) {
                $numberPart .= mt_rand(0, 9);
            }
            $code = $namePart . $numberPart;
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /* =========================================================================
       3. ELOQUENT RELATIONSHIPS
       ========================================================================= */

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(KycDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function bank(): HasOne
    {
        return $this->hasOne(BankDetail::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function bankUpdateRequests(): HasMany
    {
        return $this->hasMany(BankUpdateRequest::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function referralVisits(): HasMany
    {
        return $this->hasMany(ReferralVisit::class, 'affiliate_id');
    }

    public function commissionRules(): HasMany
    {
        return $this->hasMany(CommissionRule::class, 'affiliate_id');
    }

    public function affiliateSettings(): HasOne
    {
        return $this->hasOne(UserAffiliateSetting::class);
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'payments', 'user_id', 'bundle_id')
            ->wherePivot('status', 'success')
            ->withTimestamps();
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function beginnerGuideViews(): HasMany
    {
        return $this->hasMany(BeginnerGuideView::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['status', 'unlocked_at', 'claimed_at', 'admin_notes'])
            ->withTimestamps();
    }

    /* =========================================================================
       4. ACCESSORS & MUTATORS
       ========================================================================= */

    public function getNameAttribute($value): string
    {
        return ucwords(strtolower($value));
    }

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_photo_url) {
            return asset('storage/' . $this->profile_photo_url);
        }

        return $this->profile_picture
            ? asset('storage/' . $this->profile_picture)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&size=128&bold=true';
    }

    public function getWalletBalanceAttribute(): float
    {
        $lastTransaction = $this->walletTransactions()->latest('id')->first();
        return $lastTransaction ? (float) $lastTransaction->balance_after : 0.00;
    }

    public function getTotalEarningsAttribute(): float
    {
        return (float) $this->commissions()->sum('amount');
    }

    public function getNextAchievementAttribute(): ?Achievement
    {
        $totalEarned = $this->total_earnings;

        return Achievement::active()
            ->where('target_amount', '>', $totalEarned)
            ->orderBy('priority', 'asc')
            ->orderBy('target_amount', 'asc')
            ->first();
    }

    public function getLatestUnlockedAchievementAttribute(): ?Achievement
    {
        return $this->achievements()
            ->wherePivot('status', 'unlocked')
            ->orderBy('priority', 'desc')
            ->orderBy('target_amount', 'desc')
            ->first();
    }

    /* =========================================================================
       5. ACHIEVEMENTS & FINANCIAL DOMAIN LOGIC
       ========================================================================= */

    public function getEarningsInRange($startDate = null, $endDate = null): float
    {
        $query = $this->commissions();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Get the total target amount of claimed achievements in a given date range.
     */
    public function getClaimedRewardsSumInRange($startDate = null, $endDate = null): float
    {
        $query = $this->achievements()->wherePivot('status', 'claimed');

        if ($startDate || $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where(function ($sub) use ($startDate) {
                        $sub->where('user_achievements.claimed_at', '>=', $startDate)
                            ->orWhereNull('achievements.end_date')
                            ->orWhere('achievements.end_date', '>=', $startDate);
                    });
                }

                if ($endDate) {
                    $q->where(function ($sub) use ($endDate) {
                        $sub->where('user_achievements.claimed_at', '<=', $endDate)
                            ->orWhereNull('achievements.start_date')
                            ->orWhere('achievements.start_date', '<=', $endDate);
                    });
                }
            });
        }

        return (float) $query->sum('achievements.target_amount');
    }

    /**
     * Get available net earnings for claiming new achievements (Earned - Claimed).
     */
    public function getAvailableEarningsForRewards($startDate = null, $endDate = null): float
    {
        $earned = $this->getEarningsInRange($startDate, $endDate);
        $claimed = $this->getClaimedRewardsSumInRange($startDate, $endDate);

        return max(0, $earned - $claimed);
    }

    /* =========================================================================
       6. BUNDLE ACCESS, UNLOCKS & UPGRADE ENGINE
       ========================================================================= */

    /**
     * Get IDs of bundles directly purchased by the user.
     */
    public function purchasedBundleIds(): array
    {
        return $this->bundles()->pluck('bundles.id')->toArray();
    }

    /**
     * Get the highest preference_index among purchased bundles.
     */
    public function maxBundlePreferenceIndex(): int
    {
        if ($this->relationLoaded('bundles')) {
            return (int) ($this->bundles->max('preference_index') ?? 0);
        }
        return (int) ($this->bundles()->max('preference_index') ?? 0);
    }

    /**
     * Get IDs of all bundles unlocked via purchase or preference logic.
     */
    public function unlockedBundleIds(): array
    {
        $maxPref = $this->maxBundlePreferenceIndex();

        // Cache this per-instance to prevent repeated queries in the same request
        return once(fn() => Bundle::where('preference_index', '<=', $maxPref)
            ->where('is_published', true)
            ->pluck('id')
            ->toArray());
    }

    /**
     * Get IDs of all courses unlocked via bundles or direct purchase.
     */
    public function unlockedCourseIds(): array
    {
        // 1. Courses from unlocked bundles
        $unlockedBundles = Bundle::whereIn('id', $this->unlockedBundleIds())->with('courses')->get();
        $bundleCourseIds = $unlockedBundles->flatMap(function ($bundle) {
            return $bundle->getAllCoursesFlat()->pluck('id');
        })->unique()->toArray();

        // 2. Direct course purchases
        $directCourseIds = Payment::where('user_id', $this->id)
            ->where('status', 'success')
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray();

        return array_unique(array_merge($bundleCourseIds, $directCourseIds));
    }

    /**
     * Get the highest purchased bundle object.
     */
    public function highestPurchasedBundle(): ?Bundle
    {
        $maxPref = $this->maxBundlePreferenceIndex();
        if ($maxPref <= 0) {
            return null;
        }

        foreach ($this->bundles as $bundle) {
            if ($bundle->preference_index == $maxPref) {
                return $bundle;
            }
        }

        return Bundle::where('preference_index', $maxPref)->first();
    }

    /**
     * Get total cumulative lifetime amount paid across all successful bundle purchases.
     */
    public function totalBundlePaymentsPaid(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments
                ->where('status', 'success')
                ->whereNotNull('bundle_id')
                ->sum('total_amount');
        }

        return (float) Payment::where('user_id', $this->id)
            ->where('status', 'success')
            ->whereNotNull('bundle_id')
            ->sum('total_amount');
    }

    /**
     * Get the user's first/initial bundle purchase payment record.
     */
    public function firstBundlePayment(): ?Payment
    {
        if ($this->relationLoaded('payments')) {
            return $this->payments
                ->where('status', 'success')
                ->whereNotNull('bundle_id')
                ->sortBy('created_at')
                ->first();
        }

        return Payment::where('user_id', $this->id)
            ->where('status', 'success')
            ->whereNotNull('bundle_id')
            ->oldest('created_at')
            ->first();
    }

    /**
     * Get the latest payment for the user's highest bundle.
     */
    public function maxBundlePayment(): ?Payment
    {
        $highestBundle = $this->highestPurchasedBundle();
        if (!$highestBundle) {
            return null;
        }

        if ($this->relationLoaded('payments')) {
            return $this->payments
                ->where('status', 'success')
                ->where('bundle_id', $highestBundle->id)
                ->sortByDesc('created_at')
                ->first();
        }

        return Payment::where('user_id', $this->id)
            ->where('status', 'success')
            ->where('bundle_id', $highestBundle->id)
            ->latest('created_at')
            ->first();
    }

    /**
     * Check if user is currently eligible for time-bound bundle upgrades.
     */
    public function canUpgradeBundles(): bool
    {
        $timeLeft = $this->upgradeTimeLeftSeconds();
        return $timeLeft !== null && $timeLeft > 0;
    }

    /**
     * Calculate remaining upgrade time dynamically based on FIRST initial bundle purchase.
     * Formula: Remaining = Admin_Window_Hours - Elapsed_Hours_Since_First_Purchase
     * Timer NEVER resets on subsequent upgrades.
     */
    public function upgradeTimeLeftSeconds(): ?int
    {
        $highestBundle = $this->highestPurchasedBundle();
        if (!$highestBundle) {
            return null;
        }

        // Use the initial first bundle purchase time as the immutable baseline
        $firstPayment = $this->firstBundlePayment();
        $referenceTime = $firstPayment ? $firstPayment->created_at : $this->created_at;
        if (!$referenceTime) {
            return null;
        }

        // Fetch current global window (defaults to 24 if not set)
        $windowHours = (int) Setting::get('upgrade_window_hours', 24);
        if ($windowHours <= 0) {
            return 0;
        }

        // Calculate elapsed time from initial purchase to now
        $elapsedSeconds = $referenceTime->diffInSeconds(now(), true);
        $windowSeconds = $windowHours * 3600;

        $remainingSeconds = $windowSeconds - $elapsedSeconds;

        return $remainingSeconds > 0 ? (int) $remainingSeconds : 0;
    }

    /* =========================================================================
       7. AUTHENTICATION & SECURITY HELPERS
       ========================================================================= */

    /**
     * Determine if the user has two factor authentication enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }
}
