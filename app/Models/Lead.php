<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'referral_code',
        'gender',
        'dob',
        'state',
        'pincode',
        'product_preference',
        'ip_address',
    ];

    protected $casts = [
        'product_preference' => 'array',
        'dob' => 'date',
    ];
}
