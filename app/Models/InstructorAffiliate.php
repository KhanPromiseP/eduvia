<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InstructorAffiliate extends Model
{
    protected $fillable = [
        'instructor_id','referral_code','referral_link',
        'clicks','conversions','commission_earned'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}


