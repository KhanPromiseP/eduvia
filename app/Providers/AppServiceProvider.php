<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\Ad;

use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
    Course::class => CoursePolicy::class,
    ];

    /**
     * Bootstrap any application services.
     */

   public function boot()
{
    View::composer('*', function ($view) {
        $now = Carbon::now();

        $ads = Ad::where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->get();

        $view->with('ads', $ads);
    });
}
}
