<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    // Mass assignable columns
    protected $fillable = [
        'name',
        'code',
        'country_code'
    ];

    /**
     * Relationship: Ek State ke under bahut saare Users ho sakte hain.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
