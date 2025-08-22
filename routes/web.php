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




Route::middleware(['auth'])->group(function () {
    Route::get('/secure-content/{attachment}', [SecureContentController::class, 'stream'])
         ->name('secure.content');

    // Route::get('/download-content/{attachment}', [SecureContentController::class, 'download'])
    //      ->name('download.content');
});




// Add these test routes for development
Route::middleware(['auth'])->group(function () {
    // Regular purchase route
    Route::post('/purchase/{course}', [PaymentController::class, 'purchase'])
         ->name('purchase.course');
    
    // Test purchase route (for development only)
    Route::post('/test-purchase/{course}', [PaymentController::class, 'testPurchase'])
         ->name('test.purchase.course');
         
    // Quick purchase route for testing
    Route::get('/quick-purchase/{course}', function (Course $course) {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if ($course->isPurchasedBy($user)) {
            return redirect()->route('userdashboard')
                             ->with('info', 'You already own this course.');
        }
        
        // Create purchase record
        \App\Models\UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount_paid' => 0.00, // Free for testing
            'purchased_at' => now(),
        ]);
        
        return redirect()->route('userdashboard')
                         ->with('success', 'Test purchase completed! Course added to your dashboard.');
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


});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdController::class, 'generalAnalytics'])
            ->name('dashboard'); 


    // Route::get('//ads/analytics', [AdController::class, 'generalAnalytics'])->name('admin.ads.analytics');
    Route::resource('ads', AdController::class);
    Route::resource('products', ProductController::class);
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
    Route::post('/purchase/{course}', [PaymentController::class, 'purchase'])->name('purchase.course');
    Route::get('/learn/{course}', [LearningController::class, 'show'])->name('courses.learn');
});




Route::middleware(['auth'])->group(function() {
    // Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
   


    Route::get('checkout/{product}', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('checkout/{product}', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('download/{product}', [ProductController::class, 'download'])->name('products.download');
});


require __DIR__.'/auth.php';


