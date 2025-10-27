<?php

namespace App\Console\Commands;

use App\Models\Instructor;
use App\Models\Payment;
use App\Models\UserCourse;
use App\Models\InstructorEarning;
use Illuminate\Console\Command;

class FixInstructorEarnings extends Command
{
    protected $signature = 'earnings:fix';
    protected $description = 'Fix instructor earnings data and create missing records';

    public function handle()
    {
        $this->info('Starting earnings fix...');

        $instructors = Instructor::with('courses')->get();

        foreach ($instructors as $instructor) {
            $this->info("Processing instructor: {$instructor->user->name}");

            $courseIds = $instructor->courses()->pluck('id');

            // Get all completed payments for this instructor's courses
            $payments = Payment::where('status', 'completed')
                ->whereHas('userCourse', function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->with('userCourse.course')
                ->get();

            foreach ($payments as $payment) {
                // Check if earnings record already exists
                $existingEarning = InstructorEarning::where('payment_id', $payment->id)->first();

                if (!$existingEarning && $payment->userCourse && $payment->userCourse->course) {
                    // Calculate earnings (70% for instructor, 30% platform fee)
                    $instructorShare = $payment->amount * 0.70;
                    $platformFee = $payment->amount * 0.30;

                    InstructorEarning::create([
                        'instructor_id' => $instructor->id,
                        'payment_id' => $payment->id,
                        'course_id' => $payment->userCourse->course_id,
                        'amount' => $instructorShare,
                        'platform_fee' => $platformFee,
                        'total_amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'status' => InstructorEarning::STATUS_PROCESSED,
                        'processed_at' => $payment->completed_at ?? now(),
                    ]);

                    $this->info("Created earning record for payment: {$payment->id}");
                }
            }
        }

        $this->info('Earnings fix completed!');
        return Command::SUCCESS;
    }
}