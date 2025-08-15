<?php

namespace App\Providers;

use App\Services\AdService;
use App\View\Composers\AdComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AdService::class);
    }

    public function boot()
    {
        // Register view composer for all views
        View::composer('*', AdComposer::class);
    }
}
