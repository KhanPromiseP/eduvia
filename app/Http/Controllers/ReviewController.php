<?php
// app/Http/Controllers/ReviewController.php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        Log::info('Review store method called', [
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'request_data' => $request->all()
        ]);

        try {
            // Check if user has purchased the course
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to be logged in to leave a review.'
                ], 401);
            }

            if (!$course->isPurchasedBy(auth()->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to purchase this course to leave a review.'
                ], 403);
            }

            // Check if user has already reviewed this course
            if (Auth::user()->hasReviewed($course->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this course.'
                ], 422);
            }

            // Use the imported Validator facade
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Creating review...');
            $review = Review::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'rating' => (int) $request->rating,
                'comment' => $request->comment,
                'is_verified' => true,
                'is_approved' => true,
            ]);

            Log::info('Review created successfully', ['review_id' => $review->id]);

            // Update course rating stats
            Log::info('Updating course rating stats...');
            $course->load('approvedReviews');
            $course->updateRatingStats();
            $course->refresh();

            Log::info('Course stats updated', [
                'average_rating' => $course->average_rating,
                'total_reviews' => $course->total_reviews
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your review!',
                'review' => $review->load('user'),
                'stats' => [
                    'average_rating' => (float) $course->average_rating,
                    'total_reviews' => (int) $course->total_reviews,
                    'rating_breakdown' => $course->rating_breakdown
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating review: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'course_id' => $course->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Review $review)
    {
        Log::info('Review update method called', [
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        try {
            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // Use Validator facade for consistent error handling
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed in update', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Updating review...');
            $review->update([
                'rating' => (int) $request->rating,
                'comment' => $request->comment,
            ]);

            // Update course rating stats
            $course = $review->course;
            $course->load('approvedReviews');
            $course->updateRatingStats();
            $course->refresh();

            Log::info('Review updated successfully', ['review_id' => $review->id]);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'review' => $review->load('user'),
                'stats' => [
                    'average_rating' => (float) $course->average_rating,
                    'total_reviews' => (int) $course->total_reviews,
                    'rating_breakdown' => $course->rating_breakdown
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating review: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'review_id' => $review->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Review $review)
    {
        Log::info('Review destroy method called', [
            'review_id' => $review->id,
            'user_id' => auth()->id()
        ]);

        try {
            if ($review->user_id !== Auth::id() && !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            $course = $review->course;
            $review->delete();

            // Update course rating stats
            $course->load('approvedReviews');
            $course->updateRatingStats();
            $course->refresh();

            Log::info('Review deleted successfully', ['review_id' => $review->id]);

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully!',
                'stats' => [
                    'average_rating' => (float) $course->average_rating,
                    'total_reviews' => (int) $course->total_reviews,
                    'rating_breakdown' => $course->rating_breakdown
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting review: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'review_id' => $review->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}