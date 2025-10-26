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

    // Relationship with UserProgress to track course progress
    public function progress()
    {
        return $this->hasOne(UserProgress::class, 'user_id', 'user_id')
                    ->whereHas('module', function($query) {
                        $query->where('course_id', $this->course_id);
                    });
    }

    // Alternative approach - if you want to track progress differently
    public function userProgress()
    {
        return $this->hasManyThrough(
            UserProgress::class,
            CourseModule::class,
            'course_id', // Foreign key on CourseModule table
            'module_id', // Foreign key on UserProgress table
            'course_id', // Local key on UserCourse table
            'id' // Local key on CourseModule table
        );
    }
}