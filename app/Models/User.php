<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\AffiliateCommission;
use App\Models\KycDetail;
use App\Models\BankDetail;
use App\Models\WalletTransaction;
use App\Models\ReferralVisit;
use App\Models\CommissionRule;
use App\Models\UserAffiliateSetting;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

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
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'dob' => 'date',
        'kyc_status' => 'string',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });
    }

    private static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

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

    public function getProfileImageUrlAttribute(): string
    {
        return $this->profile_picture
            ? asset('storage/' . $this->profile_picture)
            : asset('assets/images/default-avatar.png');
    }
    public function kyc()
    {
        return $this->hasOne(KycDetail::class);
    }

    public function bank()
    {
        return $this->hasOne(BankDetail::class);
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

    public function getWalletBalanceAttribute(): float
    {
        // Ideally cache this or have a separate balance column updated via transactions
        // For now, summing transactions is safest for accuracy until scale requires optimization
        // But wait, the wallet_transactions table has balance_after.
        // So we can just get the last transaction's balance_after.

        $lastTransaction = $this->walletTransactions()->latest('id')->first();
        return $lastTransaction ? $lastTransaction->balance_after : 0.00;
    }

    public function affiliateSettings()
    {
        return $this->hasOne(UserAffiliateSetting::class);
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'payments', 'user_id', 'bundle_id')
            ->wherePivot('status', 'success')
            ->withTimestamps();
    }

    /**
     * Get IDs of bundles directly purchased by the user.
     */
    public function purchasedBundleIds()
    {
        return $this->bundles()->pluck('bundles.id')->toArray();
    }

    /**
     * Get the highest preference_index among purchased bundles.
     */
    public function maxBundlePreferenceIndex()
    {
        return $this->bundles()->max('preference_index') ?? 0;
    }

    /**
     * Get IDs of all bundles unlocked via purchase or preference logic.
     */
    public function unlockedBundleIds()
    {
        $maxPref = $this->maxBundlePreferenceIndex();
        return Bundle::where('preference_index', '<=', $maxPref)
            ->where('is_published', true)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get IDs of all courses unlocked via bundles or direct purchase.
     */
    public function unlockedCourseIds()
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
}
