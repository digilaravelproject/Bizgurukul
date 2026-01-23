<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AffiliateCommission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'reference_type',
        'reference_id',
        'amount',
        'status',
        'notes',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function markAsPaid(string $note = null): void
    {
        $this->update([
            'status' => 'paid',
            'processed_at' => now(),
            'notes' => $note ?? $this->notes
        ]);
    }
}
