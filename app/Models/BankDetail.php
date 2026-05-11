<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_holder_name',
        'account_type',
        'account_number',
        'ifsc_code',
        'upi_id',
        'document_path',
        'status',
        'admin_note',
        'verified_at',
        'is_verified'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
