<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instructor;
use App\Models\UserCourse;
use App\Models\InstructorApplication;
use App\Models\InstructorDocument;
use App\Models\Course;
use App\Models\UserProgress;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PayoutController;
use App\Models\InstructorPayout;
use App\Models\InstructorEarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class InstructorController extends Controller
{
// Instructor Dashboard
public function dashboard()
{
    if (!Auth::user()->hasRole('instructor')) {
        return redirect()->route('instructor.welcome');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    // FIXED: Get ALL courses CREATED by the instructor (including non-approved)
    $allInstructorCourseIds = Course::where('user_id', Auth::id())->pluck('id');
    
    // Get only APPROVED & PUBLISHED courses for revenue/enrollment stats
    $approvedInstructorCourseIds = Course::where('user_id', Auth::id())
        ->where('status', Course::STATUS_APPROVED)
        ->where('is_published', true)
        ->pluck('id');

    $stats = [
        'total_courses' => Course::where('user_id', Auth::id())->count(), // ALL courses (even non-approved)
        'total_students' => UserCourse::whereIn('course_id', $approvedInstructorCourseIds)
            ->distinct('user_id')
            ->count('user_id'),
        'total_revenue' => Payment::where('status', 'completed')
            ->whereHas('userCourse', function($query) use ($approvedInstructorCourseIds) {
                $query->whereIn('course_id', $approvedInstructorCourseIds);
            })
            ->sum('amount'),
        'average_rating' => $instructor->rating ?? 0,
    ];

    return view('instructor.dashboard', compact('instructor', 'stats'));
}


// Instructor Analytics
public function analytics()
{
    if (!Auth::user()->hasRole('instructor')) {
        return redirect()->route('instructor.welcome');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    // FIXED: Get ALL courses CREATED by the instructor (including non-approved)
    $allInstructorCourseIds = Course::where('user_id', Auth::id())->pluck('id');
    
    // Get only APPROVED & PUBLISHED courses for revenue/enrollment stats
    $approvedInstructorCourseIds = Course::where('user_id', Auth::id())
        ->where('status', Course::STATUS_APPROVED)
        ->where('is_published', true)
        ->pluck('id');

    // Recent Activity Data
    $recentEnrollments = UserCourse::whereIn('course_id', $approvedInstructorCourseIds)
        ->with(['user', 'course'])
        ->orderBy('purchased_at', 'desc')
        ->take(5)
        ->get();

    $recentPayments = Payment::where('status', 'completed')
        ->whereHas('userCourse', function($query) use ($approvedInstructorCourseIds) {
            $query->whereIn('course_id', $approvedInstructorCourseIds);
        })
        ->with(['user', 'userCourse.course'])
        ->orderBy('completed_at', 'desc')
        ->take(5)
        ->get();

    $recentReviews = \App\Models\Review::whereHas('course', function($query) use ($approvedInstructorCourseIds) {
            $query->whereIn('id', $approvedInstructorCourseIds);
        })
        ->with(['user', 'course'])
        ->where('is_approved', true)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    $stats = [
        'total_courses' => Course::where('user_id', Auth::id())->count(),
        'total_students' => UserCourse::whereIn('course_id', $approvedInstructorCourseIds)
            ->distinct('user_id')
            ->count('user_id'),
        'total_enrollments' => UserCourse::whereIn('course_id', $approvedInstructorCourseIds)->count(),
        'total_revenue' => Payment::where('status', 'completed')
            ->whereHas('userCourse', function($query) use ($approvedInstructorCourseIds) {
                $query->whereIn('course_id', $approvedInstructorCourseIds);
            })
            ->sum('amount'),
        'published_courses' => Course::where('user_id', Auth::id())
            ->where('status', Course::STATUS_APPROVED)
            ->where('is_published', true)
            ->count(),
        'average_rating' => $instructor->rating ?? 0,
        'recent_enrollments' => $recentEnrollments,
        'recent_payments' => $recentPayments,
        'recent_reviews' => $recentReviews,
    ];

    return view('instructor.analytics', compact('instructor', 'stats'));
}

    // Welcome page for potential instructors
    public function welcome()
    {
        $existingApplication = InstructorApplication::where('user_id', Auth::id())->first();
        $isInstructor = Auth::user()->hasRole('instructor');

        if ($isInstructor) {
            return redirect()->route('instructor.dashboard');
        }

        return view('instructor.welcome', compact('existingApplication'));
    }

    // Show step 1 of application form
    public function create()
    {
        if (Auth::user()->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }

        $existingApplication = InstructorApplication::where('user_id', Auth::id())->first();
        if ($existingApplication) {
            return redirect()->route('instructor.application.status');
        }

        return view('instructor.apply-step1');
    }

    // Store step 1 (Basic Info)
    public function storeStep1(Request $request)
    {
        $request->validate([
            'headline' => 'required|string|max:255',
            'bio' => 'required|min:100|max:500',
            'expertise' => 'required|string|max:255',
        ]);

        session([
            'application.step1' => $request->only(['headline', 'bio', 'expertise'])
        ]);

        return redirect()->route('instructor.apply.step2');
    }

    // Show step 2 (Skills & Languages)
    public function step2()
    {
        if (!session('application.step1')) {
            return redirect()->route('instructor.apply');
        }

        return view('instructor.apply-step2');
    }

    // Store step 2
    public function storeStep2(Request $request)
    {
        $request->validate([
            'skills' => 'required|array|min:3',
            'skills.*' => 'string|max:50',
            'languages' => 'required|array|min:1',
            'languages.*' => 'string|max:50',
        ]);

        $cleanedSkills = array_filter($request->skills, function($skill) {
            return !empty(trim($skill));
        });

        session([
            'application.step2' => [
                'skills' => $cleanedSkills,
                'languages' => $request->languages
            ]
        ]);

        return redirect()->route('instructor.apply.step3');
    }

    // Show step 3 (Links & Final)
    public function step3()
    {
        if (!session('application.step2')) {
            return redirect()->route('instructor.apply');
        }

        return view('instructor.apply-step3');
    }

    public function storeStep3(Request $request)
    {
        $request->validate([
            'linkedin_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'video_intro' => 'nullable|url',
            'agree_terms' => 'required|accepted',
        ]);

        session([
            'application.step3' => $request->only(['linkedin_url', 'website_url', 'video_intro'])
        ]);

        return redirect()->route('instructor.apply.step4');
    }

    // Show step 4 (Document Upload)
    public function step4()
    {
        if (!session('application.step3')) {
            return redirect()->route('instructor.apply.step3');
        }

        return view('instructor.apply-step4');
    }

   public function storeStep4(Request $request)
    {
        $request->validate([
            'id_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'certificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $step1 = session('application.step1');
                $step2 = session('application.step2');
                $step3 = session('application.step3');

                $application = InstructorApplication::create([
                    'user_id' => Auth::id(),
                    'bio' => $step1['bio'],
                    'expertise' => $step1['expertise'],
                    'linkedin_url' => $step3['linkedin_url'] ?? null,
                    'website_url' => $step3['website_url'] ?? null,
                    'video_intro' => $step3['video_intro'] ?? null,
                    'status' => 'pending',
                ]);

                $instructor = Instructor::create([
                    'user_id' => Auth::id(),
                    'headline' => $step1['headline'],
                    'bio' => $step1['bio'],
                    'skills' => $step2['skills'],
                    'languages' => $step2['languages'],
                    'is_verified' => false,
                ]);

                $this->uploadDocuments($instructor, $request);
                session()->forget(['application.step1', 'application.step2', 'application.step3']);
            });

            // Redirect to payout setup instead of status page
            return redirect()->route('instructor.apply.step5')
                ->with('success', 'Documents uploaded successfully! Now set up your payout method.');

        } catch (\Exception $e) {
            \Log::error('Failed to submit instructor application: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload documents: ' . $e->getMessage());
        }
    }

    // Helper method to handle document uploads
    private function uploadDocuments($instructor, $request)
    {
        $documents = [
            'id_card' => 'id_card',
            'passport' => 'passport', 
            'certificate' => 'certificate'
        ];

        foreach ($documents as $field => $type) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = $file->store("instructor_documents/{$instructor->id}", 'public');
                
                InstructorDocument::create([
                    'instructor_id' => $instructor->id,
                    'document_type' => $type,
                    'file_path' => $path,
                    'status' => 'pending',
                ]);
            }
        }
    }



    // Show step 5 (Payout Setup)
 public function step5()
{
    // check if application exists
    $application = InstructorApplication::where('user_id', Auth::id())->first();
    
    if (!$application) {
        return redirect()->route('instructor.apply.step1');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    $payout = $instructor->payouts ?? null;

    return view('instructor.apply-step5', compact('payout'));
}

    // Store step 5 (Payout Setup)
public function storeStep5(Request $request)
{
    \Log::info('Step 5 form submitted', $request->all());
    
    // For Tranzak Wallet, set default values before validation
    if ($request->payout_method === 'tranzak_wallet') {
        $request->merge([
            'account_name' => Auth::user()->name,
            'account_number' => 'tranzak_wallet_' . Auth::id(),
            'operator' => 'Tranzak'
        ]);
    }

    $request->validate([
        'payout_method' => 'required|in:mobile_money,bank_account,tranzak_wallet',
        'account_name' => 'required_if:payout_method,mobile_money,bank_account|string|max:255',
        'account_number' => 'required_if:payout_method,mobile_money,bank_account|string|max:255',
        'operator' => 'required_if:payout_method,mobile_money,bank_account',
        'currency' => 'required|in:XAF,USD,EUR',
        'auto_payout' => 'boolean',
        'payout_threshold' => 'numeric|min:0',
        'agree_terms' => 'required|accepted',
    ]);

    \Log::info('Validation passed');

    try {
        DB::transaction(function () use ($request) {
            $instructor = Instructor::where('user_id', Auth::id())->first();
            
            if (!$instructor) {
                throw new \Exception('Instructor not found');
            }

            // For Tranzak Wallet, ensure values are set
            $accountName = $request->account_name;
            $accountNumber = $request->account_number;
            $operator = $request->operator;

            if ($request->payout_method === 'tranzak_wallet') {
                $accountName = Auth::user()->name;
                $accountNumber = 'tranzak_wallet_' . Auth::id();
                $operator = 'Tranzak';
            }

            $payoutData = [
                'instructor_id' => $instructor->id,
                'payout_method' => $request->payout_method,
                'account_name' => $accountName,
                'account_number' => $accountNumber,
                'operator' => $operator,
                'currency' => $request->currency,
                'auto_payout' => $request->boolean('auto_payout'),
                'payout_threshold' => $request->payout_threshold ?? 0,
                'is_verified' => true, // Set to true for now
            ];

            // Create payout settings
            InstructorPayout::updateOrCreate(
                ['instructor_id' => $instructor->id],
                $payoutData
            );

            // Complete the application process
            $application = InstructorApplication::where('user_id', Auth::id())->first();
            if ($application) {
                $application->update([
                    'payout_setup_completed' => true,
                    'status' => 'pending' 
                ]);
            }
        });

        \Log::info('Step 5 completed successfully for user: ' . Auth::id());
        return redirect()->route('instructor.application.status')
            ->with('success', 'Payout setup completed! Your application is now under review.');

    } catch (\Exception $e) {
        \Log::error('Payout setup failed: ' . $e->getMessage());
        \Log::error('Exception trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to setup payout: ' . $e->getMessage())->withInput();
    }
}


    // Application status page
    public function status()
    {
        $application = InstructorApplication::where('user_id', Auth::id())
            ->with('reviewer')
            ->first();

        if (!$application) {
            return redirect()->route('instructor.apply');
        }

        return view('instructor.application-status', compact('application'));
    }

    // Instructor Courses
    public function courses()
    {
        $instructor = Instructor::where('user_id', Auth::id())->first();
        $courses = Auth::user()->courses()->withCount('userCourses')->latest()->get();
        
        return view('instructor.courses.index', compact('instructor', 'courses'));
    }


  public function students()
{
    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    if (!$instructor) {
        return redirect()->route('instructor.welcome')
            ->with('error', 'Instructor profile not found.');
    }

    // Get courses CREATED by this instructor (where user_id = current user id)
    $instructorCourseIds = Course::where('user_id', Auth::id())->pluck('id');
    
    // Debug: Log the course IDs to verify
    \Log::info('Instructor created courses', [
        'instructor_id' => $instructor->id,
        'user_id' => Auth::id(),
        'course_ids' => $instructorCourseIds->toArray(),
        'courses_count' => $instructorCourseIds->count()
    ]);

    // Get students enrolled in THIS instructor's created courses only
    $students = User::whereHas('userCourses', function($query) use ($instructorCourseIds) {
            $query->whereIn('course_id', $instructorCourseIds);
        })
        ->withCount(['userCourses' => function($query) use ($instructorCourseIds) {
            $query->whereIn('course_id', $instructorCourseIds);
        }])
        ->with(['userCourses' => function($query) use ($instructorCourseIds) {
            $query->whereIn('course_id', $instructorCourseIds)
                ->with('course');
        }])
        ->distinct()
        ->get();

    // Debug: Log the students count
    \Log::info('Students found for instructor', [
        'instructor_id' => $instructor->id,
        'students_count' => $students->count()
    ]);

    return view('instructor.students.index', compact('instructor', 'students'));
}
    // Student Detail View
    public function studentDetail($studentId)
    {
        $instructor = Instructor::where('user_id', Auth::id())->first();
        
        // Get instructor course IDs
        $instructorCourseIds = Auth::user()->courses()->pluck('courses.id');
        
        // Get specific student with their enrolled courses from this instructor
        $student = User::whereHas('userCourses', function($query) use ($instructorCourseIds) {
                $query->whereIn('course_id', $instructorCourseIds);
            })
            ->with(['userCourses' => function($query) use ($instructorCourseIds) {
                $query->whereIn('course_id', $instructorCourseIds)
                    ->with(['course', 'course.modules']) // Load course modules for duration calculation
                    ->orderBy('purchased_at', 'desc');
            }])
            ->findOrFail($studentId);
        
        // Calculate student statistics
        $totalCoursesEnrolled = $student->userCourses->count();
        $completedCourses = 0;
        $totalTimeSpent = 0;
        
        // Pre-calculate progress for each enrollment
        foreach ($student->userCourses as $enrollment) {
            $progressData = calculateCourseProgress($enrollment->course_id, $studentId);
            
            // Add progress data to enrollment object
            $enrollment->completion_percentage = $progressData['completion_percentage'];
            $enrollment->time_spent = $progressData['total_time_spent'];
            $enrollment->completed_modules = $progressData['completed_modules'];
            $enrollment->total_modules = $progressData['total_modules'];
            
            if ($progressData['completion_percentage'] == 100) {
                $completedCourses++;
            }
            $totalTimeSpent += $progressData['total_time_spent'];
        }
        
        return view('instructor.students.detail', compact(
            'instructor', 
            'student', 
            'totalCoursesEnrolled', 
            'completedCourses', 
            'totalTimeSpent'
        ));
    }
    // Helper method to calculate course progress
    private function calculateCourseProgress($courseId, $userId)
    {
        $course = Course::with('modules')->find($courseId);
        $totalModules = $course->modules->count();
        
        if ($totalModules == 0) {
            return [
                'completion_percentage' => 0,
                'total_time_spent' => 0
            ];
        }
        
        // Get all progress records for this course
        $progressRecords = UserProgress::where('user_id', $userId)
            ->whereHas('module', function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->get();
        
        $completedModules = $progressRecords->where('completed', true)->count();
        
        // Calculate total time spent based on viewed_at and completed_at
        $totalTimeSpent = 0;
        foreach ($progressRecords as $progress) {
            if ($progress->viewed_at && $progress->completed_at) {
                // Calculate time between viewing and completing (in seconds)
                $timeSpent = $progress->completed_at->diffInSeconds($progress->viewed_at);
                $totalTimeSpent += $timeSpent;
            } elseif ($progress->viewed_at) {
                // If not completed, count time from viewing to now
                $timeSpent = now()->diffInSeconds($progress->viewed_at);
                $totalTimeSpent += $timeSpent;
            }
        }
        
        $completionPercentage = ($completedModules / $totalModules) * 100;
        
        return [
            'completion_percentage' => round($completionPercentage, 2),
            'total_time_spent' => $totalTimeSpent
        ];
    }


    // Instructor Earnings
    // Instructor Earnings - FIXED VERSION (Only Approved Courses)
public function earnings()
{
    if (!Auth::user()->hasRole('instructor')) {
        return redirect()->route('instructor.welcome');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    // FIXED: Get APPROVED courses CREATED by the instructor
    $instructorCourseIds = Course::where('user_id', Auth::id())
        ->where('status', Course::STATUS_APPROVED) // Only approved courses
        ->where('is_published', true) // Only published courses
        ->pluck('id');

    // Use InstructorEarning model for earnings data
    $totalEarnings = $instructor->total_earnings;
    $pendingEarnings = $instructor->pending_earnings;
    $processedEarnings = $instructor->processed_earnings;

    // Monthly earnings breakdown from InstructorEarning model
    $monthlyEarnings = InstructorEarning::where('instructor_id', $instructor->id)
        ->where('status', InstructorEarning::STATUS_PAID_OUT)
        ->selectRaw('SUM(amount) as total, MONTH(paid_out_at) as month, YEAR(paid_out_at) as year')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    // FIXED: Course-wise earnings - Only show APPROVED courses CREATED by instructor
    $courseEarnings = Course::where('user_id', Auth::id()) // Only courses created by this instructor
        ->where('status', Course::STATUS_APPROVED) // Only approved courses
        ->where('is_published', true) // Only published courses
        ->with(['instructorEarnings' => function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id)
                  ->where('status', InstructorEarning::STATUS_PAID_OUT);
        }])
        ->get()
        ->map(function($course) {
            $courseEarnings = $course->instructorEarnings->sum('amount');
            $enrollmentsCount = UserCourse::where('course_id', $course->id)->count();
            
            $course->total_earnings = $courseEarnings;
            $course->enrollments_count = $enrollmentsCount;
            
            return $course;
        });

    // Recent earnings transactions
    $recentTransactions = InstructorEarning::where('instructor_id', $instructor->id)
        ->with(['payment.user', 'course'])
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    // Payout information
    $payout = $instructor->payouts;

    // Calculate platform statistics - FIXED: Only count APPROVED courses created by instructor
    $platformStats = [
        'total_courses' => Course::where('user_id', Auth::id())
            ->where('status', Course::STATUS_APPROVED)
            ->where('is_published', true)
            ->count(),
        'total_students' => UserCourse::whereIn('course_id', $instructorCourseIds)
            ->distinct('user_id')
            ->count('user_id'),
        'total_enrollments' => UserCourse::whereIn('course_id', $instructorCourseIds)->count(),
        'completion_rate' => $this->calculateCompletionRate($instructorCourseIds),
    ];

    return view('instructor.earnings.index', compact(
        'instructor', 
        'totalEarnings', 
        'monthlyEarnings',
        'courseEarnings',
        'recentTransactions',
        'payout',
        'pendingEarnings',
        'processedEarnings',
        'platformStats'
    ));
}

    private function calculateCompletionRate($courseIds)
    {
        $totalEnrollments = UserCourse::whereIn('course_id', $courseIds)->count();
        if ($totalEnrollments === 0) return 0;

        $completedEnrollments = UserCourse::whereIn('course_id', $courseIds)
            ->whereHas('progress', function($query) {
                $query->where('completion_percentage', '>=', 80); // 80% considered completed
            })
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

   

// Public Instructor Profile

public function show($userId)
{  
    // Find instructor by user_id
    $instructor = Instructor::where('user_id', $userId)
        ->with(['user', 'courses' => function($query) {
            $query->where('is_published', true);
        }])
        ->withCount([
            'courses as courses_count' => function($query) {
                $query->where('is_published', true);
            }
        ])
        ->active()
        ->firstOrFail();

    // Calculate statistics
    $instructor->rating = \App\Models\InstructorReview::where('instructor_id', $instructor->id)
        ->where('is_approved', true)
        ->avg('rating') ?? 0;

    $instructor->total_reviews = \App\Models\InstructorReview::where('instructor_id', $instructor->id)
        ->where('is_approved', true)
        ->count();

    $instructor->followers_count = DB::table('instructor_followers')
        ->where('instructor_id', $instructor->id)
        ->count();

    // Calculate total students
    $courseIds = $instructor->courses()->pluck('id');
    $instructor->total_students = UserCourse::whereIn('course_id', $courseIds)
        ->distinct('user_id')
        ->count('user_id');

    // Get ALL data for ALL tabs upfront
    $data = [
        'instructor' => $instructor,
        
        // Courses data - load all courses data
        'courses' => $instructor->courses()
            ->where('is_published', true)
            ->where('status', \App\Models\Course::STATUS_APPROVED)
            ->withCount(['enrollments as enrollments_count'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get(),

        // Reviews data - load all reviews
        'reviews' => class_exists('App\Models\InstructorReview') 
            ? \App\Models\InstructorReview::with(['user', 'course'])
                ->where('instructor_id', $instructor->id)
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
            : \App\Models\Review::whereHas('course', function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id);
                })
                ->with(['user', 'course'])
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10),

        // Students data (only for instructor) - FIXED QUERY
        'students' => auth()->check() && auth()->id() === $instructor->user_id
            ? User::whereHas('userCourses', function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->withCount(['userCourses as courses_count' => function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                }])
                ->distinct()
                ->paginate(20)
            : collect(),

        // Recent courses for overview
        'recentCourses' => $instructor->courses()
            ->where('is_published', true)
            ->where('status', \App\Models\Course::STATUS_APPROVED)
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
    ];

    return view('instructor.profile', $data);
}

public function contact(Request $request)
{
    $request->validate([
        'instructor_id' => 'required|exists:instructors,id',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10|max:1000'
    ]);

    // Handle contact form submission
    // need to implement the actual email sending logic here

    return response()->json([
        'success' => true,
        'message' => 'Your message has been sent successfully!'
    ]);
}

public function getTabContent($user_id, $tab)
{
    try {
        $instructor = Instructor::where('user_id', $user_id)->firstOrFail();
        
        $html = '';
        
        switch ($tab) {
            case 'overview':
                $html = $this->getOverviewTabContent($instructor);
                break;
            case 'courses':
                $html = $this->getCoursesTabContent($instructor);
                break;
            case 'reviews':
                $html = $this->getReviewsTabContent($instructor);
                break;
            case 'students':
                $html = $this->getStudentsTabContent($instructor);
                break;
            case 'contact':
                $html = $this->getContactTabContent($instructor);
                break;
            default:
                $html = '<div class="text-center text-gray-500 py-8">Tab not found</div>';
        }
        
        // Return plain HTML instead of JSON to avoid CORS issues
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('X-Content-Type-Options', 'nosniff');
        
    } catch (\Exception $e) {
        \Log::error('Error loading tab content: ' . $e->getMessage());
        return response('<div class="text-center text-red-500 py-8">Error loading content</div>')
            ->header('Content-Type', 'text/html');
    }
}






// Add this method to debug tab content
public function debugTabContent($user_id, $tab)
{
    try {
        $instructor = Instructor::where('user_id', $user_id)->firstOrFail();
        
        \Log::info("Debug tab content - Tab: {$tab}, Instructor ID: {$instructor->id}");
        
        switch ($tab) {
            case 'courses':
                $courses = $instructor->courses()
                    ->where('is_published', true)
                    ->where('status', \App\Models\Course::STATUS_APPROVED)
                    ->withCount(['enrollments as enrollments_count'])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                \Log::info("Courses count: " . $courses->count());
                \Log::info("Courses data: " . json_encode($courses->toArray()));
                break;
                
            case 'reviews':
                if (class_exists('App\Models\InstructorReview')) {
                    $reviews = \App\Models\InstructorReview::with(['user', 'course'])
                        ->where('instructor_id', $instructor->id)
                        ->where('is_approved', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                } else {
                    $reviews = \App\Models\Review::whereHas('course', function($query) use ($instructor) {
                            $query->where('instructor_id', $instructor->id);
                        })
                        ->with(['user', 'course'])
                        ->where('is_approved', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                }
                
                \Log::info("Reviews count: " . $reviews->count());
                break;
                
            case 'students':
                $students = $instructor->enrolledStudents()
                    ->withCount(['userCourses as courses_count' => function($query) use ($instructor) {
                        $query->whereIn('course_id', $instructor->courses->pluck('id'));
                    }])
                    ->paginate(20);
                
                \Log::info("Students count: " . $students->count());
                break;
        }
        
        return $this->getTabContent($user_id, $tab);
        
    } catch (\Exception $e) {
        \Log::error('Debug tab error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'html' => '<div class="text-center text-red-500 py-8">Error: ' . $e->getMessage() . '</div>'
        ], 500);
    }
}






private function getOverviewTabContent($instructor)
{
    try {
        // Get recent courses for activity
        $recentCourses = $instructor->courses()
            ->where('is_published', true)
            ->where('status', \App\Models\Course::STATUS_APPROVED)
            ->withCount('enrollments') // Use 'enrollments' not 'userCourses'
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('instructor.overview-tab', [
            'recentCourses' => $recentCourses,
            'instructor' => $instructor
        ])->render();
    } catch (\Exception $e) {
        \Log::error('Error loading overview tab: ' . $e->getMessage());
        return '<div class="text-center text-red-500 py-8">Error loading overview content</div>';
    }
}

private function getCoursesTabContent($instructor)
{
    $courses = $instructor->courses()
        ->where('is_published', true)
        ->where('status', \App\Models\Course::STATUS_APPROVED) // Only approved courses
        ->withCount(['enrollments as enrollments_count']) // Use 'enrollments' not 'userCourses'
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->with('category')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('instructor.courses-tab', [
        'courses' => $courses,
        'instructor' => $instructor
    ])->render();
}


private function getReviewsTabContent($instructor)
{
    // Check if InstructorReview model exists, otherwise use Course reviews
    if (class_exists('App\Models\InstructorReview')) {
        $reviews = \App\Models\InstructorReview::with(['user', 'course'])
            ->where('instructor_id', $instructor->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    } else {
        // Fallback to course reviews
        $reviews = \App\Models\Review::whereHas('course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with(['user', 'course'])
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    return view('instructor.reviews-tab', [
        'reviews' => $reviews,
        'instructor' => $instructor
    ])->render();
}

private function getStudentsTabContent($instructor)
{
    // Only show to the instructor themselves
    if (auth()->id() !== $instructor->user_id) {
        return '<div class="text-center text-gray-500 py-8">Access denied.</div>';
    }

    // Get students using the fixed relationship
    $students = $instructor->getEnrolledStudents()
        ->withCount(['userCourses as courses_count' => function($query) use ($instructor) {
            $query->whereIn('course_id', $instructor->courses()->pluck('id'));
        }])
        ->with(['userCourses.course']) // Load user courses with course info
        ->paginate(20);

    return view('instructor.students-tab', [
        'students' => $students,
        'instructor' => $instructor
    ])->render();
}

private function getContactTabContent($instructor)
{
    try {
        // Only show to authenticated users who are not the instructor
        if (!auth()->check() || auth()->id() === $instructor->user_id) {
            return '<div class="text-center text-gray-500 py-8">Access denied.</div>';
        }

        return view('instructor.contact-tab', [
            'instructor' => $instructor
        ])->render();
    } catch (\Exception $e) {
        \Log::error('Error loading contact tab: ' . $e->getMessage());
        return '<div class="text-center text-red-500 py-8">Error loading contact form</div>';
    }
}

public function follow(Request $request, Instructor $instructor) // Use route model binding
{
    $user = auth()->user();

    if ($request->follow) {
        DB::table('instructor_followers')->insertOrIgnore([
            'instructor_id' => $instructor->id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } else {
        DB::table('instructor_followers')
            ->where('instructor_id', $instructor->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    $followersCount = DB::table('instructor_followers')
        ->where('instructor_id', $instructor->id)
        ->count();

    return response()->json([
        'success' => true,
        'followers_count' => $followersCount
    ]);
}

public function sendMessage(Request $request)
{
    $request->validate([
        'instructor_id' => 'required|exists:instructors,id',
        'message' => 'required|string|min:10|max:1000'
    ]);

    $message = InstructorMessage::create([
        'instructor_id' => $request->instructor_id,
        'user_id' => auth()->id(),
        'message' => $request->message
    ]);

    // You might want to send email notification here

    return response()->json([
        'success' => true,
        'message' => 'Your message has been sent successfully!'
    ]);
}

public function submitReview(Request $request)
{
    try {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|min:10|max:1000'
        ]);

        \Log::info('Review submission attempt', [
            'user_id' => auth()->id(),
            'instructor_id' => $request->instructor_id,
            'rating' => $request->rating
        ]);

        // Get the instructor to find their user_id
        $instructor = Instructor::find($request->instructor_id);
        if (!$instructor) {
            return response()->json([
                'success' => false,
                'message' => 'Instructor not found.'
            ], 404);
        }

        // Check if user has enrolled in any course from this instructor
        // Use user_id instead of instructor_id since courses are linked to users
        $hasEnrolled = UserCourse::where('user_id', auth()->id())
            ->whereHas('course', function($query) use ($instructor) {
                $query->where('user_id', $instructor->user_id); // Use user_id here
            })
            ->exists();

        \Log::info('Enrollment check', [
            'user_id' => auth()->id(),
            'instructor_user_id' => $instructor->user_id,
            'has_enrolled' => $hasEnrolled
        ]);

        if (!$hasEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You need to enroll in at least one course from this instructor before leaving a review.'
            ], 403);
        }

        // Check for existing review
        $existingReview = \App\Models\InstructorReview::where('instructor_id', $request->instructor_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this instructor.'
            ], 403);
        }

        // Get one course ID for the relationship
        $userCourse = UserCourse::where('user_id', auth()->id())
            ->whereHas('course', function($query) use ($instructor) {
                $query->where('user_id', $instructor->user_id);
            })
            ->first();

        if (!$userCourse) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to find your course enrollment.'
            ], 404);
        }

        \App\Models\InstructorReview::create([
            'instructor_id' => $request->instructor_id,
            'user_id' => auth()->id(),
            'course_id' => $userCourse->course_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'is_approved' => true // Or set based on your approval system
        ]);

        \Log::info('Review submitted successfully');

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your review has been submitted successfully.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Review submission error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Sorry, we encountered an error while submitting your review. Please try again.'
        ], 500);
    }
}


public function updateReview(Request $request)
{
    try {
        $request->validate([
            'review_id' => 'required|exists:instructor_reviews,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|min:10|max:1000'
        ]);

        $review = \App\Models\InstructorReview::find($request->review_id);
        
        // Check if the review belongs to the current user
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own reviews.'
            ], 403);
        }

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your review has been updated successfully.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Review update error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Sorry, we encountered an error while updating your review.'
        ], 500);
    }
}

public function deleteReview($reviewId)
{
    try {
        $review = \App\Models\InstructorReview::find($reviewId);
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.'
            ], 404);
        }

        // Check if the review belongs to the current user
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own reviews.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your review has been deleted successfully.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Review delete error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Sorry, we encountered an error while deleting your review.'
        ], 500);
    }
}


// Instructor Reviews Management - COMPREHENSIVE FIX
public function reviews()
{
    if (!Auth::user()->hasRole('instructor')) {
        return redirect()->route('instructor.welcome');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    if (!$instructor) {
        return redirect()->route('instructor.welcome')
            ->with('error', 'Instructor profile not found.');
    }

    // Get instructor course IDs - FIXED: Use instructor's user_id to get courses
    $instructorCourseIds = Course::where('user_id', Auth::id())->pluck('id');
    
    \Log::info('Instructor reviews request', [
        'instructor_id' => $instructor->id,
        'user_id' => Auth::id(),
        'course_ids' => $instructorCourseIds->toArray()
    ]);

    // Get reviews for instructor's courses with proper relationships
    $reviews = \App\Models\Review::whereIn('course_id', $instructorCourseIds)
        ->with(['user' => function($query) {
            $query->select('id', 'name', 'email', 'profile_path');
        }, 'course' => function($query) {
            $query->select('id', 'title', 'user_id');
        }])
        ->where('is_approved', true)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    \Log::info('Reviews found', ['count' => $reviews->count()]);

    // Calculate statistics
    $totalReviews = $reviews->total();
    $averageRating = \App\Models\Review::whereIn('course_id', $instructorCourseIds)
        ->where('is_approved', true)
        ->avg('rating') ?? 0;

    // Rating distribution
    $ratingDistribution = [];
    for ($i = 1; $i <= 5; $i++) {
        $ratingDistribution[$i] = \App\Models\Review::whereIn('course_id', $instructorCourseIds)
            ->where('is_approved', true)
            ->where('rating', $i)
            ->count();
    }

    $fiveStarReviews = $ratingDistribution[5] ?? 0;
    $recentReviews = \App\Models\Review::whereIn('course_id', $instructorCourseIds)
        ->where('is_approved', true)
        ->where('created_at', '>=', now()->subDays(30))
        ->count();

    // Response rate - check if column exists
    $responseRate = 0;
    if (\Schema::hasColumn('reviews', 'instructor_response')) {
        $reviewsWithResponse = \App\Models\Review::whereIn('course_id', $instructorCourseIds)
            ->where('is_approved', true)
            ->whereNotNull('instructor_response')
            ->count();
        $responseRate = $totalReviews > 0 ? round(($reviewsWithResponse / $totalReviews) * 100) : 0;
    }

    // Get instructor's courses for filter
    $courses = Course::where('user_id', Auth::id())->get(['id', 'title']);

    return view('instructor.reviews.index', compact(
        'instructor',
        'reviews',
        'totalReviews',
        'averageRating',
        'ratingDistribution',
        'fiveStarReviews',
        'recentReviews',
        'responseRate',
        'courses'
    ));
}

// Reply to review
public function replyToReview(Request $request, $reviewId)
{
    $review = \App\Models\Review::findOrFail($reviewId);
    
    // Verify the review belongs to instructor's course
    $instructorCourseIds = Auth::user()->courses()->pluck('courses.id');
    if (!in_array($review->course_id, $instructorCourseIds->toArray())) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    $request->validate([
        'response' => 'required|string|min:10|max:1000'
    ]);

    $review->update([
        'instructor_response' => $request->response,
        'response_date' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Response submitted successfully.'
    ]);
}

// Report review
public function reportReview(Request $request)
{
    $request->validate([
        'review_id' => 'required|exists:reviews,id',
        'reason' => 'required|string',
        'details' => 'nullable|string'
    ]);

    // Verify the review belongs to instructor's course
    $instructorCourseIds = Auth::user()->courses()->pluck('courses.id');
    $review = \App\Models\Review::find($request->review_id);
    
    if (!in_array($review->course_id, $instructorCourseIds->toArray())) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    // Create report
    \App\Models\ReviewReport::create([
        'review_id' => $request->review_id,
        'reporter_id' => Auth::id(),
        'reason' => $request->reason,
        'details' => $request->details,
        'status' => 'pending'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Review reported successfully.'
    ]);
}


// Instructor Followers Management - Eloquent Version (Fixed)
public function followers()
{
    if (!Auth::user()->hasRole('instructor')) {
        return redirect()->route('instructor.welcome');
    }

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    if (!$instructor) {
        return redirect()->route('instructor.welcome')
            ->with('error', 'Instructor profile not found.');
    }

    // Get followers using Eloquent relationship with proper error handling
    $followers = $instructor->followers()
        ->withCount(['userCourses as enrolled_courses_count' => function($query) use ($instructor) {
            $instructorCourseIds = $instructor->courses()->pluck('id');
            $query->whereIn('course_id', $instructorCourseIds);
        }])
        ->orderBy('instructor_followers.created_at', 'desc')
        ->paginate(20);

    // Add is_student property to each follower and ensure data integrity
    $followers->getCollection()->transform(function ($follower) {
        // Ensure follower is not null
        if (!$follower) {
            return null;
        }
        
        $follower->is_student = $follower->enrolled_courses_count > 0;
        return $follower;
    })->filter(); // Remove any null entries

    // Calculate statistics
    $totalFollowers = $instructor->followers()->count();
    $activeStudents = $followers->where('is_student', true)->count();
    
    $newThisMonth = $instructor->followers()
        ->where('instructor_followers.created_at', '>=', now()->subDays(30))
        ->count();

    return view('instructor.followers.index', compact(
        'instructor',
        'followers',
        'totalFollowers',
        'activeStudents',
        'newThisMonth'
    ));
}
// Send message to follower
public function messageFollower(Request $request)
{
    $request->validate([
        'recipient_id' => 'required|exists:users,id',
        'message' => 'required|string|min:10|max:1000'
    ]);

    $instructor = Instructor::where('user_id', Auth::id())->first();
    
    // Verify the recipient is following the instructor
    $isFollower = DB::table('instructor_followers')
        ->where('instructor_id', $instructor->id)
        ->where('user_id', $request->recipient_id)
        ->exists();

    if (!$isFollower) {
        return response()->json([
            'success' => false,
            'message' => 'User is not following you.'
        ], 403);
    }

    // Create message (need to implement messaging system)
    // For now, we just return success
  

    return response()->json([
        'success' => true,
        'message' => 'Message sent successfully.'
    ]);
}
 
}