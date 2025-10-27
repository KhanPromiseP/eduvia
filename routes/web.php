<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Course;


use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Models\Ad;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Api\AdApiController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\Admin\AdminInstructorController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Instructor\DocumentationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\Admin\AdminIncomeController;
















// for non-premium courses (free courses) quick purchase route
Route::middleware(['auth'])->group(function () {
    // Quick purchase route for free courses (non premium courses) 
    Route::get('/quick-purchase/{course}', function (Course $course) {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($course->isPurchasedBy($user)) {
            return redirect()->route('userdashboard')
                             ->with('info', "Hi {$user->name}, you already own this course!");
        }

        // Create purchase record
        \App\Models\UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount_paid' => 0.00, // Free course
            'purchased_at' => now(),
        ]);

        return redirect()->route('userdashboard')
                         ->with('success', "Great choice {$user->name}! 🎉 You’ve successfully enrolled in {$course->title}.");
    })->name('quick.purchase');
});













// Route::get('/', function () {
//     return view('welcome');
// });




Route::get('/', [DashboardController::class, 'index'])

    ->name('dashboard');





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/modal', [ProfileController::class, 'showModal'])->name('profile.modal');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/additional', [ProfileController::class, 'updateAdditional'])->name('profile.additional.update');
    Route::patch('/profile/instructor', [ProfileController::class, 'updateInstructorProfile'])->name('instructor.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/detect-location', [ProfileController::class, 'detectLocation'])->name('profile.detect-location');
});

Route::middleware(['auth', 'role:admin'])->prefix('income')->group(function () {
    Route::get('/', [AdminIncomeController::class, 'index'])->name('admin.income.index');
    Route::get('/export', [AdminIncomeController::class, 'exportReport'])->name('admin.income.export');
});

Route::middleware(['auth', 'role:admin,instructor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard'); 


    // Route::get('//ads/analytics', [AdController::class, 'generalAnalytics'])->name('admin.ads.analytics');
    Route::resource('ads', AdController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('email-campaigns', EmailCampaignController::class);
    Route::resource('subscribers', SubscriberController::class);
    // Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::resource('payments', PaymentController::class);



    // Courses
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    
    // Course modules management
    Route::get('courses/{course}/modules', [\App\Http\Controllers\Admin\CourseController::class, 'modules'])
         ->name('courses.modules');
    Route::post('courses/{course}/modules', [\App\Http\Controllers\Admin\CourseController::class, 'storeModule'])
         ->name('courses.modules.store');
    Route::put('courses/{course}/modules/{module}', [\App\Http\Controllers\Admin\CourseController::class, 'updateModule'])
         ->name('courses.modules.update');
    Route::delete('courses/{course}/modules/{module}', [\App\Http\Controllers\Admin\CourseController::class, 'destroyModule'])
         ->name('courses.modules.destroy');
    
    // Attachments
    Route::post('courses/{course}/modules/{module}/attachments', [\App\Http\Controllers\Admin\CourseController::class, 'storeAttachment'])
         ->name('courses.attachments.store');
    Route::delete('courses/{course}/modules/{module}/attachments/{attachment}', [\App\Http\Controllers\Admin\CourseController::class, 'destroyAttachment'])
         ->name('courses.attachments.destroy');
    Route::put('courses/{course}/modules/{module}/attachments/{attachment}', [\App\Http\Controllers\Admin\CourseController::class, 'updateAttachment'])
        ->name('courses.attachments.update');
    
    // Toggle publish status
    Route::post('courses/{course}/toggle-publish', [\App\Http\Controllers\Admin\CourseController::class, 'togglePublish'])
         ->name('courses.toggle-publish');
});








// Uppy upload routes
Route::post('/admin/upload/s3-params', [\App\Http\Controllers\Admin\UploadController::class, 'getS3Params'])->name('admin.upload.s3-params');
Route::post('/admin/courses/{course}/modules/{module}/attachments/uppy-store', [\App\Http\Controllers\Admin\CourseController::class, 'storeUppyAttachment'])->name('admin.courses.attachments.uppy-store');

    Route::get('/instructor/documentation', [DocumentationController::class, 'index'])->name('instructor.documentation');
    Route::post('/instructor/documentation/mark-read', [DocumentationController::class, 'markAsRead'])->name('instructor.documentation.mark-read');
    Route::get('/instructor/documentation/progress', [DocumentationController::class, 'getProgress'])->name('instructor.documentation.progress');

// Instructor Course Routes
// Instructor Course Routes (KEEP THIS ONE - it has proper naming)
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    // Courses CRUD
    Route::resource('courses', InstructorCourseController::class);


    // Course Modules Management
    Route::get('courses/{course}/modules', [InstructorCourseController::class, 'modules'])
         ->name('courses.modules');
    Route::post('courses/{course}/modules', [InstructorCourseController::class, 'storeModule'])
         ->name('courses.modules.store');
    Route::put('courses/{course}/modules/{module}', [InstructorCourseController::class, 'updateModule'])
         ->name('courses.modules.update');
    Route::delete('courses/{course}/modules/{module}', [InstructorCourseController::class, 'destroyModule'])
         ->name('courses.modules.destroy');
    
    // Course Attachments Management
    Route::post('courses/{course}/modules/{module}/attachments', [\App\Http\Controllers\Admin\CourseController::class, 'storeAttachment'])
         ->name('courses.attachments.store');
    Route::put('courses/{course}/modules/{module}/attachments/{attachment}', [\App\Http\Controllers\Admin\CourseController::class, 'updateAttachment'])
         ->name('courses.attachments.update');
    Route::delete('courses/{course}/modules/{module}/attachments/{attachment}', [\App\Http\Controllers\Admin\CourseController::class, 'destroyAttachment'])
         ->name('courses.attachments.destroy');
    
    // Course Review Process
    Route::post('courses/{course}/submit-review', [InstructorCourseController::class, 'submitForReview'])
         ->name('courses.submit-review');
    Route::post('courses/{course}/withdraw-review', [InstructorCourseController::class, 'withdrawFromReview'])
         ->name('courses.withdraw-review');
    
    // Course Analytics
    Route::get('courses/{course}/analytics', [InstructorCourseController::class, 'analytics'])
         ->name('courses.analytics');
});


// Instructor Analytics Route
Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/instructor/analytics', [InstructorController::class, 'analytics'])->name('instructor.analytics');
});


// Instructor Application Routes
Route::middleware(['auth'])->group(function () {
    // Instructor Application Process
    Route::get('/instructor/welcome', [InstructorController::class, 'welcome'])->name('instructor.welcome');
    Route::get('/instructor/apply', [InstructorController::class, 'create'])->name('instructor.apply');
    Route::post('/instructor/apply/step1', [InstructorController::class, 'storeStep1'])->name('instructor.apply.step1');
    Route::get('/instructor/apply/step2', [InstructorController::class, 'step2'])->name('instructor.apply.step2');
    Route::post('/instructor/apply/step2', [InstructorController::class, 'storeStep2'])->name('instructor.apply.step2.store');
    Route::get('/instructor/apply/step3', [InstructorController::class, 'step3'])->name('instructor.apply.step3');
    Route::post('/instructor/apply/step3', [InstructorController::class, 'storeStep3'])->name('instructor.apply.step3.store');
    Route::get('/instructor/apply/step4', [InstructorController::class, 'step4'])->name('instructor.apply.step4');
    Route::post('/instructor/apply/step4', [InstructorController::class, 'storeStep4'])->name('instructor.apply.step4.store');
    Route::get('/instructor/apply/step5', [InstructorController::class, 'step5'])->name('instructor.apply.step5');
    Route::post('/instructor/apply/step5', [InstructorController::class, 'storeStep5'])->name('instructor.apply.step5.store');
    Route::get('/instructor/application-status', [InstructorController::class, 'status'])->name('instructor.application.status');

    // Payout management routes
    Route::get('/instructor/payout/setup', [PayoutController::class, 'showPayoutSetup'])->name('instructor.payout.setup');
    Route::post('/instructor/payout/setup', [PayoutController::class, 'setupPayout'])->name('instructor.payout.setup.store');


        
    // Instructor Dashboard (for approved instructors)
    Route::middleware(['role:instructor'])->group(function () {
        Route::get('/instructor/dashboard', [InstructorController::class, 'dashboard'])->name('instructor.dashboard');
        Route::get('/instructor/students', [InstructorController::class, 'students'])->name('instructor.students');
        Route::get('/instructor/students/{id}', [InstructorController::class, 'studentDetail'])->name('instructor.students.detail');
        Route::get('/instructor/earnings', [InstructorController::class, 'earnings'])->name('instructor.earnings');

        
        });
});


// Instructor management routes
Route::middleware(['auth', 'verified'])->prefix('instructor')->name('instructor.')->group(function () {
    // ... existing routes ...
    
    // Reviews management
    Route::get('/reviews', [InstructorController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{review}/reply', [InstructorController::class, 'replyToReview'])->name('reviews.reply');
    Route::post('/reviews/report', [InstructorController::class, 'reportReview'])->name('reviews.report');
    
    // Followers management
    Route::get('/followers', [InstructorController::class, 'followers'])->name('followers');
    Route::post('/followers/message', [InstructorController::class, 'messageFollower'])->name('followers.message');
    
    // Profile
    // Route::get('/profile', [InstructorController::class, 'profile'])->name('profile');
    // Route::put('/profile', [InstructorController::class, 'updateProfile'])->name('profile.update');
});

// // Instructor review management routes
// Route::middleware(['auth', 'verified'])->prefix('instructor')->name('instructor.')->group(function () {
//     Route::get('/reviews', [InstructorController::class, 'reviews'])->name('reviews');
//     Route::post('/reviews/{review}/reply', [InstructorController::class, 'replyToReview'])->name('reviews.reply');
//     Route::post('/reviews/report', [InstructorController::class, 'reportReview'])->name('reviews.report');
// });


// Admin Instructor Management Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/instructors/applications', [AdminInstructorController::class, 'applications'])->name('admin.instructors.applications');
    Route::get('/instructors/applications/{application}', [AdminInstructorController::class, 'showApplication'])->name('admin.instructors.applications.show');
    
    // POST routes for approve/reject
    Route::post('/instructors/applications/{application}/approve', [AdminInstructorController::class, 'approveApplication'])->name('admin.instructors.applications.approve');
    Route::post('/instructors/applications/{application}/reject', [AdminInstructorController::class, 'rejectApplication'])->name('admin.instructors.applications.reject');
    
    Route::get('/instructors', [AdminInstructorController::class, 'index'])->name('admin.instructors.index');
    Route::get('/instructors/{instructor}', [AdminInstructorController::class, 'show'])->name('admin.instructors.show');

    // document approve
    Route::post('/instructors/documents/{document}/approve', [AdminInstructorController::class, 'approveDocument'])->name('admin.instructors.documents.approve');
    Route::post('/instructors/documents/{document}/reject', [AdminInstructorController::class, 'rejectDocument'])->name('admin.instructors.documents.reject');
    
    // Add suspend/reactivate routes
    Route::post('/instructors/{instructor}/suspend', [AdminInstructorController::class, 'suspend'])->name('admin.instructors.suspend');
    Route::post('/instructors/{instructor}/reactivate', [AdminInstructorController::class, 'reactivate'])->name('admin.instructors.reactivate');
});







Route::get('/attachment/{attachment}/download', [CourseController::class, 'downloadFile'])
    ->name('attachment.download')
    ->middleware('auth');


// File serving routes
Route::get('/attachment/{attachment}/serve', [App\Http\Controllers\Admin\CourseController::class, 'serveFile'])
    ->name('attachment.serve')
    ->middleware('auth');

// Route::get('/attachment/{attachment}/download', [App\Http\Controllers\Admin\CourseController::class, 'serveFile'])
//     ->name('attachment.download')
//     ->middleware('auth');



// In your web.php - Replace existing file routes with these:

// Unified file serving with security
// Route::middleware(['auth'])->group(function () {
    // Serve any file type with security checks
    Route::get('/attachment/{attachment}/view', [App\Http\Controllers\Admin\CourseController::class, 'serveFile'])
         ->name('attachment.view');
    
    // Download files (if allowed)
//     Route::get('/attachment/{attachment}/download', [App\Http\Controllers\Admin\CourseController::class, 'downloadFile'])
//          ->name('attachment.download');
    
//     // Secure video streaming
//     Route::get('/secure-video/{attachment}', [App\Http\Controllers\VideoStreamController::class, 'getSecureStream'])
//          ->name('secure.video.stream');
    
//     // Video progress tracking
//     Route::post('/api/video/progress', [App\Http\Controllers\VideoProgressController::class, 'track'])
//          ->name('video.progress.track');
// });

// Fix the API attachment view route
Route::post('/api/attachment/view', function (Illuminate\Http\Request $request) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    try {
        $attachmentId = $request->input('attachment_id');
        
        // Just return success for now - you can add analytics later
        return response()->json([
            'success' => true, 
            'message' => 'View tracked',
            'attachment_id' => $attachmentId
        ]);
    } catch (\Exception $e) {
        \Log::error('Attachment view tracking failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Tracking failed'
        ], 500);
    }
})->middleware('auth');

// Add the missing direct video streaming route
Route::get('/stream/video/{attachment}/direct', [App\Http\Controllers\Admin\CourseController::class, 'streamVideoDirect'])
    ->name('stream.video.direct')
    ->middleware('auth');

// // Secure streaming routes
// Route::middleware(['auth', 'streaming.throttle:60,1', 'secure.content'])->group(function () {

    // Get secure stream URL
//     Route::get('/api/secure-stream/{attachment}', [App\Http\Controllers\VideoStreamController::class, 'getSecureStream'])
//          ->name('secure.video.stream');
    
    // Stream secure video
//     Route::get('/secure-stream/{token}/{quality}', [App\Http\Controllers\VideoStreamController::class, 'streamVideo'])
//          ->name('secure.video.stream.token');
    
//     // Offline access
//     Route::post('/api/offline-access', [App\Http\Controllers\VideoStreamController::class, 'requestOfflineAccess'])
//          ->name('video.offline.access');
    
//     // Download offline
//     Route::get('/secure-offline/{token}', [App\Http\Controllers\VideoStreamController::class, 'downloadOffline'])
//          ->name('secure.video.offline');

// });


Route::get('/debug/video/{attachment}', [App\Http\Controllers\Admin\CourseController::class, 'debugVideoServe'])
     ->name('debug.video')
     ->middleware('auth');

     // Add to your routes/api.php or routes/web.php
Route::get('/api/attachment/{attachment}/stream-url', function (Attachment $attachment) {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    try {
        // For regular videos, generate a signed URL
        if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            $signedUrl = Storage::disk('r2')->temporaryUrl(
                $attachment->file_path,
                now()->addHours(2),
                [
                    'ResponseContentType' => 'video/mp4',
                    'ResponseCacheControl' => 'private, max-age=7200'
                ]
            );

            return response()->json([
                'stream_url' => $signedUrl,
                'type' => 'signed_url'
            ]);
        }

        // For other files, return the view URL
        return response()->json([
            'stream_url' => route('attachment.view', ['attachment' => $attachment->id]),
            'type' => 'direct_url'
        ]);

    } catch (\Exception $e) {
        \Log::error('Stream URL generation failed: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to generate stream URL',
            'stream_url' => route('attachment.view', ['attachment' => $attachment->id])
        ], 500);
    }
})->name('api.attachment.stream-url');

// Replace your current secure video routes with these:

// Secure video streaming routes
Route::middleware(['auth'])->group(function () {
    // Get secure stream URL - this should use the attachment ID
     Route::get('/stream/video/{attachment}/direct', [App\Http\Controllers\VideoStreamController::class, 'streamVideoDirect'])
         ->name('stream.video.direct');

    Route::get('/secure-video/{attachment}', [App\Http\Controllers\VideoStreamController::class, 'getSecureStream'])
         ->name('secure.video.stream');
    
    // Stream video with token (this is the actual streaming endpoint)
    Route::get('/stream/{token}/{quality}', [App\Http\Controllers\VideoStreamController::class, 'streamVideo'])
         ->name('secure.video.play');
    
    // Offline access
    Route::post('/api/offline-access', [App\Http\Controllers\VideoStreamController::class, 'requestOfflineAccess'])
         ->name('video.offline.access');
    
    // Download offline
    Route::get('/secure-offline/{token}', [App\Http\Controllers\VideoStreamController::class, 'downloadOffline'])
         ->name('secure.video.offline');
});

// Progress tracking routes
Route::middleware(['auth', 'throttle:120,1'])->group(function () {
    // Track video progress
//     Route::post('/api/video/progress', [App\Http\Controllers\VideoProgressController::class, 'track'])
//          ->name('video.progress.track');
    
    // Track video events
    Route::post('/api/video/events', [App\Http\Controllers\VideoProgressController::class, 'trackEvent'])
         ->name('video.events.track');
    
    // Mark video as completed
    Route::post('/api/video/complete', [App\Http\Controllers\VideoProgressController::class, 'markComplete'])
         ->name('video.complete');
    
    // Get user progress
    Route::get('/api/progress/course/{courseId}', [App\Http\Controllers\VideoProgressController::class, 'getUserProgress'])
         ->name('user.progress.course');
});

// Attachment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/api/attachment/complete', [App\Http\Controllers\AttachmentController::class, 'markComplete'])
         ->name('attachment.complete');
    
    Route::get('/api/attachment/{attachmentId}/analytics', [App\Http\Controllers\AttachmentController::class, 'getAnalytics'])
         ->name('attachment.analytics');
    
    Route::get('/api/module/{moduleId}/progress', [App\Http\Controllers\AttachmentController::class, 'getUserModuleProgress'])
         ->name('user.module.progress');
});




// Instructor Course Routes
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->group(function () {
    Route::get('courses/{course}/modules', [\App\Http\Controllers\Instructor\InstructorCourseController::class, 'modules'])
         ->name('instructor.courses.modules');
    Route::post('courses/{course}/submit-review', [\App\Http\Controllers\Instructor\InstructorCourseController::class, 'submitForReview'])
         ->name('instructor.courses.submit-review');
    Route::post('courses/{course}/withdraw-review', [\App\Http\Controllers\Instructor\InstructorCourseController::class, 'withdrawFromReview'])
         ->name('instructor.courses.withdraw-review');
         
});

// Admin Course Review Routes
Route::middleware(['auth', 'role:admin,reviewer'])->group(function () {
    Route::get('/courses/pending-review', [AdminCourseController::class, 'pendingReview'])
         ->name('admin.courses.pending-review');
    Route::post('/courses/{course}/approve', [AdminCourseController::class, 'approveCourse'])
         ->name('admin.courses.approve');
    Route::post('/courses/{course}/reject', [AdminCourseController::class, 'rejectCourse'])
         ->name('admin.courses.reject');
});


// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin,instructor'])->group(function () {
    Route::post('/courses/{course}/toggle-publish', [AdminCourseController::class, 'togglePublish'])->name('admin.courses.toggle-publish');
});

// Instructor routes  
Route::prefix('instructor')->middleware(['auth', 'instructor'])->group(function () {
    Route::post('/courses/{course}/submit-review', [InstructorCourseController::class, 'submitForReview'])->name('instructor.courses.submit-review');
    Route::post('/courses/{course}/withdraw-review', [InstructorCourseController::class, 'withdrawFromReview'])->name('instructor.courses.withdraw-review');
});


// Ad tracking routes
Route::prefix('ads')->group(function () {
    Route::post('/track/view', [AdController::class, 'trackView']);
    Route::post('/track/click', [AdController::class, 'trackClick']);
    Route::post('/track/close', [AdController::class, 'trackClose']);
    Route::post('/track/page-visit', [AdApiController::class, 'trackPageVisit']);
    Route::post('/track/time-spent', [AdApiController::class, 'trackTimeSpent']);
    
    Route::get('/placement/{placement}', [AdController::class, 'getAdsForPlacement']);
    Route::get('/analytics', [AdController::class, 'analytics']);
})->withoutMiddleware(['auth:admin']); //routes should be public







// Other Public Routes
// Public Product Routes (using Admin\ProductController)
Route::controller(\App\Http\Controllers\Admin\ProductController::class)->group(function () {
    Route::get('/products', 'publicIndex')->name('products.index');
    Route::get('/products/{product}', 'publicShow')->name('products.show');
});
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.submit');
Route::get('/service', [ServiceController::class, 'index'])->name('service.index');









Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/userdashboard', [UserDashboardController::class, 'index'])->name('userdashboard');
    // Route::post('/purchase/{course}', [PaymentController::class, 'purchase'])->name('purchase.course');
    Route::get('/learn/{course}', [LearningController::class, 'show'])->name('courses.learn');




});



// routes/web.php
Route::middleware(['auth'])->group(function () {
    // Route::post('/course/{course}/purchase', [PaymentController::class, 'initiatePayment'])
    //     ->name('purchase.course');
    Route::post('/payment/initiate/{course}', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'handleCancel'])->name('payment.cancel');

    Route::get('/payment/invoice/{payment}', [PaymentController::class, 'showInvoice'])->name('payment.invoice');
    Route::get('/payment/invoice/{payment}/download', [PaymentController::class, 'downloadInvoice'])->name('payment.invoice.download');
  
});

// Webhook route
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])
    ->name('payment.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);








// Route::middleware(['auth'])->group(function() {
//     // Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
   

//     Route::get('checkout/{product}', [CheckoutController::class, 'show'])->name('checkout');
//     Route::post('checkout/{product}', [CheckoutController::class, 'pay'])->name('checkout.pay');
//     Route::get('download/{product}', [ProductController::class, 'download'])->name('products.download');


    
// });


// course review routes
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});


// Instructor Profile Routes
Route::get('/instructor/{user_id}', [InstructorController::class, 'show'])->name('instructor.profile');
Route::get('/instructor/{user_id}/tab/{tab}', [InstructorController::class, 'getTabContent'])->name('instructor.tab.content');

// Instructor Actions
Route::post('/instructor/{instructor}/follow', [InstructorController::class, 'follow'])->name('instructor.follow');
Route::post('/instructor/{instructor}/unfollow', [InstructorController::class, 'unfollow'])->name('instructor.unfollow');
Route::post('/instructor/message', [InstructorController::class, 'sendMessage'])->name('instructor.message');
Route::post('/instructor/review', [InstructorController::class, 'submitReview'])->name('instructor.review.submit');
Route::post('/instructor/review/update', [InstructorController::class, 'updateReview'])->name('instructor.review.update');
Route::delete('/instructor/review/{review}', [InstructorController::class, 'deleteReview'])->name('instructor.review.delete');
Route::post('/instructor/contact', [InstructorController::class, 'contact'])->name('instructor.contact');

// Add these routes if they don't exist
Route::get('/instructor/students/{id}', [InstructorController::class, 'studentDetail'])->name('instructor.students.detail');
Route::get('/instructor/reviews', [InstructorController::class, 'reviews'])->name('instructor.reviews');

require __DIR__.'/auth.php';

