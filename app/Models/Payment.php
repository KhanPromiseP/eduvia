<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Payment status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'course_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'tranzak_response',
        'completed_at'
    ];

    protected $casts = [
        'tranzak_response' => 'array',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function userCourse()
    {
        return $this->hasOne(UserCourse::class, 'payment_id');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted($paymentMethod = null, $responseData = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'payment_method' => $paymentMethod,
            'tranzak_response' => $responseData,
            'completed_at' => now()
        ]);

        return $this;
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute()
    {
        return match($this->payment_method) {
            'mobile_money' => 'Mobile Money',
            'card' => 'Credit/Debit Card',
            'bank' => 'Bank Transfer',
            default => ucfirst($this->payment_method) ?? 'Unknown'
        };
    }
}