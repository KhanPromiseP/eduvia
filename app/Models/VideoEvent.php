<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoEvent extends Model
{
    use HasFactory;

    protected $table = 'video_events';

    protected $fillable = [
        'user_id',
        'attachment_id',
        'session_id',
        'event_type',
        'current_time',
        'quality',
        'user_agent',
        'ip_address',
        'occurred_at'
    ];

    protected $casts = [
        'current_time' => 'decimal:3',
        'occurred_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }
}