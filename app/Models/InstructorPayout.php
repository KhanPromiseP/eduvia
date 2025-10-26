<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InstructorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id','payment_method','payment_email',
        'bank_account','momo_number','currency'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}

