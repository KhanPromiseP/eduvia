<?php
// app/Models/InstructorMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'user_id',
        'message',
        'response',
        'responded_at',
        'is_public'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'is_public' => 'boolean'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}