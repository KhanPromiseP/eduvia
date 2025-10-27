<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id', 'payment_id', 'course_id', 'amount',
        'platform_fee', 'total_amount', 'currency', 'status',
        'processed_at', 'paid_out_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_out_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';
    const STATUS_PAID_OUT = 'paid_out';

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }

    public function scopeForPayout($query)
    {
        return $query->where('status', self::STATUS_PROCESSED)
                    ->whereNull('paid_out_at');
    }

    public function markAsProcessed()
    {
        $this->update([
            'status' => self::STATUS_PROCESSED,
            'processed_at' => now()
        ]);
    }

    public function markAsPaidOut()
    {
        $this->update([
            'status' => self::STATUS_PAID_OUT,
            'paid_out_at' => now()
        ]);
    }
}