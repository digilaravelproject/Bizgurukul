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
        'city',
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
}
