<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\PaymentController;
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



Route::middleware(['auth'])->group(function() {
    // Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
   


    Route::get('checkout/{product}', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('checkout/{product}', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('download/{product}', [ProductController::class, 'download'])->name('products.download');
});


require __DIR__.'/auth.php';


