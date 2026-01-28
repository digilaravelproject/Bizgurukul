<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDetail extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'pan_name', 'document_path', 'document_type', 'admin_note', 'status', 'verified_at'];
}
