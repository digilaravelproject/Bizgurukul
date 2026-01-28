<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'bank_name', 'account_holder_name', 'account_number', 'ifsc_code', 'upi_id', 'is_verified'];
}
