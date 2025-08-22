<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function purchase(Course $course)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')
                                 ->with('error', 'Please login to purchase courses.');
            }

            $user = Auth::user();
            
            // Check if user already purchased this course
            if ($course->isPurchasedBy($user)) {
                return redirect()->route('userdashboard')
                                 ->with('info', 'You already own this course.');
            }
            
            // For testing purposes - create a purchase record without actual payment
            UserCourse::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_paid' => $course->price,
                'purchased_at' => now(),
            ]);
            
            // Log the purchase for testing
            Log::info('Course purchased', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_title' => $course->title,
                'amount' => $course->price
            ]);
            
            return redirect()->route('userdashboard')
                             ->with('success', 'Course purchased successfully! You can now access all course content.');
            
        } catch (\Exception $e) {
            Log::error('Purchase failed: ' . $e->getMessage());
            
            return redirect()->back()
                             ->with('error', 'Purchase failed. Please try again.');
        }
    }

    /**
     * Alternative method for testing without payment processing
     * This can be used during development
     */
    public function testPurchase(Course $course)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $user = Auth::user();
            
            if ($course->isPurchasedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course already purchased'
                ], 400);
            }
            
            $purchase = UserCourse::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_paid' => 0.00, // Free for testing
                'purchased_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test purchase completed',
                'purchase_id' => $purchase->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }
}