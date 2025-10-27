// In app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Process payouts on the last day of every month at 9 AM
    $schedule->command('payouts:process-monthly')
             ->monthlyOn(null, '9:00')
             ->timezone('Africa/Douala');
}