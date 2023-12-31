<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'otp', 'expire_at'];


    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
