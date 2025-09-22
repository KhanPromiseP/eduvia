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
use App\Http\Controllers\SecureContentController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;




Route::middleware(['auth'])->group(function () {
    Route::get('/secure-content/{attachment}', [SecureContentController::class, 'stream'])
         ->name('secure.content');

    // Route::get('/download-content/{attachment}', [SecureContentController::class, 'download'])
    //      ->name('download.content');
});










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
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/additional', [ProfileController::class, 'updateAdditional'])
    ->name('profile.additional.update');
    Route::post('/profile/detect-location', [ProfileController::class, 'detectLocation'])
    ->name('profile.detect-location');


});

Route::middleware(['auth', 'admin'])
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
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
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



require __DIR__.'/auth.php';

