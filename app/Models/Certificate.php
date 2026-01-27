<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['user_id', 'certificate_no', 'file_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
