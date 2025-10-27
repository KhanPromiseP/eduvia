<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instructor;
use App\Models\InstructorDocument;
use App\Models\InstructorApplication;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\Review;
use App\Models\InstructorEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminInstructorController extends Controller
{
    // Show all instructor applications
    public function applications()
    {
        $applications = InstructorApplication::with(['user', 'reviewer'])
            ->latest()
            ->paginate(10);

        $pendingCount = InstructorApplication::where('status', 'pending')->count();
        $approvedCount = InstructorApplication::where('status', 'approved')->count();
        $rejectedCount = InstructorApplication::where('status', 'rejected')->count();

        return view('admin.instructors.applications', compact('applications', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    // Show single application
    public function showApplication(InstructorApplication $application)
    {
        $application->load(['user', 'reviewer']);
        $instructor = Instructor::where('user_id', $application->user_id)->first();
        
        return view('admin.instructors.application-show', compact('application', 'instructor'));
    }

    // Approve application
    public function approveApplication(Request $request, InstructorApplication $application)
    {
        // Validate the request
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($application, $validated) {
                // Update application status
                $application->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'review_notes' => $validated['review_notes'] ?? null,
                ]);

                // Activate instructor profile
                $instructor = Instructor::where('user_id', $application->user_id)->first();
                if ($instructor) {
                    $instructor->update([
                        'is_verified' => true,
                        'headline' => $instructor->headline ?? 'Instructor at ' . config('app.name'),
                    ]);
                } else {
                    // Create instructor profile if it doesn't exist
                    Instructor::create([
                        'user_id' => $application->user_id,
                        'headline' => 'Instructor at ' . config('app.name'),
                        'bio' => $application->bio,
                        'skills' => [],
                        'languages' => [],
                        'is_verified' => true,
                    ]);
                }

                // Assign instructor role to user
                $user = User::find($application->user_id);
                if (!$user->hasRole('instructor')) {
                    $user->assignRole('instructor');
                }

                // To add notification logic here later
                // $user->notify(new InstructorApplicationApproved($application));
            });

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor application approved successfully! The user can now access instructor features.');

        } catch (\Exception $e) {
            \Log::error('Failed to approve instructor application: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    // Reject application
    public function rejectApplication(Request $request, InstructorApplication $application)
    {
        // Validate the request
        $validated = $request->validate([
            'review_notes' => 'required|string|min:10|max:1000',
        ]);

        try {
            $application->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'review_notes' => $validated['review_notes'],
            ]);

            // To add notification logic here later
            // $application->user->notify(new InstructorApplicationRejected($application));

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor application rejected successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to reject instructor application: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    // List all approved instructors
       public function index()
    {
        // Get instructors with their user relationship
        $instructors = Instructor::with(['user'])
            ->where('is_verified', true)
            ->latest()
            ->paginate(10);

        // Debug: Check what we're getting
        \Log::info('Instructors found:', [
            'count' => $instructors->count(),
            'instructors' => $instructors->pluck('user_id')->toArray()
        ]);

        // Calculate statistics PROPERLY
        $totalCourses = 0;
        $totalStudents = 0;
        $totalRatings = 0;
        $instructorCount = 0;

        foreach ($instructors as $instructor) {
            // Get courses created by this instructor (using user_id)
            $instructorCourses = Course::where('user_id', $instructor->user_id)
                ->where('status', Course::STATUS_APPROVED)
                ->where('is_published', true)
                ->get();

            // Count courses for this instructor
            $instructorCoursesCount = $instructorCourses->count();
            
            // Get course IDs for student count
            $courseIds = $instructorCourses->pluck('id');

            // Calculate total students for this instructor
            $instructorStudents = DB::table('user_courses')
                ->whereIn('course_id', $courseIds)
                ->distinct('user_id')
                ->count('user_id');

            // Calculate average rating for this instructor
            $instructorRating = Review::whereIn('course_id', $courseIds)
                ->where('is_approved', true)
                ->avg('rating') ?? 0;

            // Add to instructor object for display in table
            $instructor->courses_count = $instructorCoursesCount;
            $instructor->display_total_students = $instructorStudents;
            $instructor->display_rating = $instructorRating;
            $instructor->display_total_reviews = Review::whereIn('course_id', $courseIds)
                ->where('is_approved', true)
                ->count();

            // Update running totals for overall statistics
            $totalCourses += $instructorCoursesCount;
            $totalStudents += $instructorStudents;
            $totalRatings += $instructorRating;
            $instructorCount++;

            // Debug each instructor
            \Log::info("Instructor {$instructor->user->name}:", [
                'user_id' => $instructor->user_id,
                'courses_count' => $instructorCoursesCount,
                'students_count' => $instructorStudents,
                'rating' => $instructorRating,
                'course_ids' => $courseIds->toArray()
            ]);
        }

        $averageRating = $instructorCount > 0 ? $totalRatings / $instructorCount : 0;

        // Final debug
        \Log::info('Final Statistics:', [
            'total_courses' => $totalCourses,
            'total_students' => $totalStudents,
            'average_rating' => $averageRating,
            'instructor_count' => $instructorCount
        ]);

        return view('admin.instructors.index', compact(
            'instructors', 
            'totalCourses', 
            'totalStudents', 
            'averageRating'
        ));
    }

    // Show instructor details - FIXED VERSION
    public function show(Instructor $instructor)
    {
        // Get courses created by this instructor (using user_id)
        $instructorCourses = Course::where('user_id', $instructor->user_id)
            ->where('status', Course::STATUS_APPROVED)
            ->where('is_published', true)
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();

        // Add courses to instructor object
        $instructor->courses = $instructorCourses;

        // Calculate basic statistics
        $courseIds = $instructorCourses->pluck('id');
        
        $instructor->courses_count = $instructorCourses->count();
        $instructor->total_students = DB::table('user_courses')
            ->whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');
        $instructor->rating = Review::whereIn('course_id', $courseIds)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;
        $instructor->total_reviews = Review::whereIn('course_id', $courseIds)
            ->where('is_approved', true)
            ->count();

        // Load other relationships
        $instructor->load([
            'user',
            'earnings' => function($query) {
                $query->latest()->take(20);
            },
            'documents',
            'payouts',
            'followers'
        ]);

        // Get instructor's own enrollments (as a student)
        $studentEnrollments = UserCourse::with('course')
            ->where('user_id', $instructor->user_id)
            ->latest()
            ->take(10)
            ->get();

        // Get reviews for instructor's courses
        $recentReviews = Review::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->where('is_approved', true)
            ->latest()
            ->take(10)
            ->get();

        // Calculate comprehensive statistics
        $stats = $this->calculateInstructorStats($instructor);

        return view('admin.instructors.show', compact(
            'instructor',
            'studentEnrollments',
            'recentReviews',
            'stats'
        ));
    }

    /**
     * Calculate comprehensive instructor statistics
     */
    private function calculateInstructorStats(Instructor $instructor)
    {
        $courseIds = $instructor->courses->pluck('id');

        return [
            // Course Statistics
            'total_courses' => $instructor->courses_count,
            'published_courses' => $instructor->courses->where('is_published', true)->count(),
            'draft_courses' => Course::where('user_id', $instructor->user_id)
                                ->where(function($query) {
                                    $query->where('status', '!=', Course::STATUS_APPROVED)
                                          ->orWhere('is_published', false);
                                })
                                ->count(),
            
            // Student Statistics
            'total_students' => $instructor->total_students,
            'recent_students' => DB::table('user_courses')
                ->whereIn('course_id', $courseIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id'),
            
            // Earnings Statistics
            'total_earnings' => $instructor->earnings->where('status', InstructorEarning::STATUS_PAID_OUT)->sum('amount'),
            'pending_earnings' => $instructor->earnings->where('status', InstructorEarning::STATUS_PROCESSED)->sum('amount'),
            'lifetime_earnings' => $instructor->earnings->sum('amount'),
            
            // Rating Statistics
            'average_rating' => $instructor->rating ?? 0,
            'total_reviews' => $instructor->total_reviews ?? 0,
            'rating_distribution' => $this->getRatingDistribution($instructor),
            
            // Engagement Statistics
            'total_followers' => $instructor->followers->count(),
            'completion_rate' => $this->calculateCompletionRate($instructor),
            
            // Performance Metrics
            'avg_course_rating' => $instructor->courses->avg('reviews_avg_rating') ?? 0,
            'best_rated_course' => $instructor->courses->sortByDesc('reviews_avg_rating')->first(),
            'most_popular_course' => $instructor->courses->sortByDesc('enrollments_count')->first(),
        ];
    }

    /**
     * Get rating distribution for instructor's courses
     */
    private function getRatingDistribution(Instructor $instructor)
    {
        $courseIds = Course::where('user_id', $instructor->user_id)
            ->where('status', Course::STATUS_APPROVED)
            ->where('is_published', true)
            ->pluck('id');

        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = Review::whereIn('course_id', $courseIds)
                ->where('rating', $i)
                ->where('is_approved', true)
                ->count();
        }
        return $distribution;
    }

    /**
     * Calculate course completion rate
     */
    private function calculateCompletionRate(Instructor $instructor)
{
    $courseIds = Course::where('user_id', $instructor->user_id)
        ->where('status', Course::STATUS_APPROVED)
        ->where('is_published', true)
        ->pluck('id');

    $totalEnrollments = DB::table('user_courses')
        ->whereIn('course_id', $courseIds)
        ->count();

    if ($totalEnrollments === 0) {
        return 0;
    }

    // OPTIMIZED: Calculate completion rate using database queries
    $completedEnrollments = 0;
    
    foreach ($courseIds as $courseId) {
        $course = Course::with('modules')->find($courseId);
        $totalModules = $course->modules->count();
        
        if ($totalModules === 0) continue;
        
        // Get students who completed all modules for this course
        $completedStudents = DB::table('user_progress')
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->where('completed', true)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT module_id) = ?', [$totalModules])
            ->count();
            
        $completedEnrollments += $completedStudents;
    }

    return round(($completedEnrollments / $totalEnrollments) * 100, 2);
}

    
    /**
     * Suspend an instructor
     */
    public function suspend(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::transaction(function () use ($instructor, $validated) {
                // NOT removing the instructor role - just marking as suspended
                $instructor->update([
                    'suspended_at' => now(),
                    'suspension_reason' => $validated['reason'],
                ]);

                // Log the suspension
                \Log::info("Instructor suspended: {$instructor->user->name}. Reason: {$validated['reason']}");
                
                // To add notification logic here
                // $instructor->user->notify(new InstructorSuspended($validated['reason']));
            });

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor suspended successfully. They can no longer access instructor features.');

        } catch (\Exception $e) {
            \Log::error('Failed to suspend instructor: ' . $e->getMessage());
            return back()->with('error', 'Failed to suspend instructor: ' . $e->getMessage());
        }
    }

    public function approveDocument(InstructorDocument $document)
    {
        try {
            $document->update([
                'status' => 'approved',
                'verified_by' => Auth::id(),
            ]);

            return back()->with('success', 'Document approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to approve document: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve document.');
        }
    }

    public function rejectDocument(InstructorDocument $document)
    {
        try {
            $document->update([
                'status' => 'rejected', 
                'verified_by' => Auth::id(),
            ]);

            return back()->with('success', 'Document rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to reject document: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject document.');
        }
    }

    /**
     * Reactivate a suspended instructor
     */
    public function reactivate(Instructor $instructor)
    {
        try {
            DB::transaction(function () use ($instructor) {
                // clearing the suspension fields - the role is still remains
                $instructor->update([
                    'suspended_at' => null,
                    'suspension_reason' => null,
                ]);

                // Log the reactivation
                \Log::info("Instructor reactivated: {$instructor->user->name}");
                
                // To add notification logic here
                // $instructor->user->notify(new InstructorReactivated());
            });

            return redirect()->route('admin.instructors.applications')
                ->with('success', 'Instructor reactivated successfully. They can now access instructor features again.');

        } catch (\Exception $e) {
            \Log::error('Failed to reactivate instructor: ' . $e->getMessage());
            return back()->with('error', 'Failed to reactivate instructor: ' . $e->getMessage());
        }
    }

    /**
     * Get instructor analytics (optional additional method)
     */
    public function analytics(Instructor $instructor)
    {
        // This could be used for detailed analytics page
        $instructor->load(['courses.enrollments', 'earnings']);

        $monthlyEarnings = $this->getMonthlyEarnings($instructor);
        $coursePerformance = $this->getCoursePerformance($instructor);

        return view('admin.instructors.analytics', compact(
            'instructor', 
            'monthlyEarnings', 
            'coursePerformance'
        ));
    }

    private function getMonthlyEarnings(Instructor $instructor)
    {
        return InstructorEarning::where('instructor_id', $instructor->id)
            ->where('status', InstructorEarning::STATUS_PAID_OUT)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();
    }

    private function getCoursePerformance(Instructor $instructor)
    {
        return $instructor->courses()
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy('enrollments_count', 'desc')
            ->get();
    }
}