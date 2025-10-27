<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Your existing artisan commands
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Add your scheduled commands here
Schedule::command('payouts:process-monthly')
    ->monthlyOn(null, '9:00')
    ->timezone('Africa/Douala')
    ->description('Process monthly instructor payouts');

// You can add more scheduled tasks here
Schedule::command('model:prune')->daily(); // Example of another scheduled command

// Optional: Health check for payouts
Schedule::command('payouts:check-balance')->weekly()->mondays()->at('8:00');