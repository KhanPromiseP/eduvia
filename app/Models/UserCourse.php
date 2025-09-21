<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCourse extends Pivot
{
    use HasFactory;

    protected $table = 'user_courses';

    protected $fillable = [
        'user_id',
        'course_id',
        'payment_id',
        'amount_paid',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'amount_paid' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Create user course enrollment from payment
     */
    public static function createFromPayment(Payment $payment)
    {
        // Check if already enrolled
        $existingEnrollment = self::where('user_id', $payment->user_id)
            ->where('course_id', $payment->course_id)
            ->first();

        if ($existingEnrollment) {
            return $existingEnrollment;
        }

        return self::create([
            'user_id' => $payment->user_id,
            'course_id' => $payment->course_id,
            'payment_id' => $payment->id,
            'amount_paid' => $payment->amount,
            'purchased_at' => now()
        ]);
    }
}