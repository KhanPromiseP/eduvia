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


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


// Route::get('/dashboard', function () {
//     $placement = 'dashboard'; // or whatever placement you want

//     $ads = Ad::active()
//         ->currentlyRunning()
//         ->where('placement', 'like', "%{$placement}%")
//         ->get();

//     return view('dashboard', compact('ads'));
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $now = Carbon::now();

    // Fetch active ads, currently running, targeted for sidebar or dashboard
    $ads = Ad::where('is_active', true)
        ->where(function($query) use ($now) {
            $query->whereNull('start_at')->orWhere('start_at', '<=', $now);
        })
        ->where(function($query) use ($now) {
            $query->whereNull('end_at')->orWhere('end_at', '>=', $now);
        })
        ->where('placement', 'like', '%dashboard%')  // or use 'sidebar' or any placement you prefer
        ->get();

    return view('dashboard', compact('ads'));
})->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', ['header' => 'Dashboard']);
    })->name('admin.dashboard');

    Route::resource('ads', AdController::class);
    Route::resource('products', ProductController::class);
    Route::resource('email-campaigns', EmailCampaignController::class);
    Route::resource('subscribers', SubscriberController::class);
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::resource('payments', PaymentController::class);
});


// Public Product Routes (using Admin\ProductController)
// Public Product Routes (using Admin\ProductController)
Route::controller(\App\Http\Controllers\Admin\ProductController::class)->group(function () {
    Route::get('/products', 'publicIndex')->name('products.index');
    Route::get('/products/{product}', 'publicShow')->name('products.show');
});




// Other Public Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::get('/services', [ContactController::class, 'show'])->name('services.show');
Route::post('/services/contact', [ContactController::class, 'submit'])->name('contact.submit');


require __DIR__.'/auth.php';


