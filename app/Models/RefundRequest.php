<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'payment_id', 'course_id', 'amount', 'currency',
        'reason', 'reason_code', 'status', 'admin_notes', 'refund_id', 'refunded_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PROCESSED = 'processed';

    // Reason codes matching Tranzak
    const REASON_CODES = [
        1 => 'Mutual agreement between buyer and seller',
        2 => 'The order did not match the transaction agreement',
        3 => 'Suspicious transaction',
        4 => 'Disputed transaction',
        5 => 'Other reasons',
        6 => 'Deficient product/service',
        7 => 'Duplicate transaction',
        8 => 'Purchased item not delivered by merchant'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getReasonTextAttribute()
    {
        return self::REASON_CODES[$this->reason_code] ?? 'Unknown reason';
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function canBeProcessed()
    {
        return $this->status === self::STATUS_APPROVED && empty($this->refund_id);
    }
}