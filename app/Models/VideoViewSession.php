<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoViewSession extends Model
{
    use HasFactory;

    protected $table = 'video_view_sessions';

    protected $fillable = [
        'user_id',
        'attachment_id',
        'video_id',
        'session_id',
        'ip_address',
        'user_agent',
        'watch_time',
        'completion_rate',
        'started_at',
        'ended_at',
        'quality_changes',
        'completed'
    ];

    protected $casts = [
        'watch_time' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'quality_changes' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'completed' => 'boolean'
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