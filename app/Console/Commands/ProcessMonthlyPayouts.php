<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PayoutController;

class ProcessMonthlyPayouts extends Command
{
    protected $signature = 'payouts:process-monthly';
    protected $description = 'Process monthly payouts to instructors';

    public function handle()
    {
        $this->info('Starting monthly payout processing...');
        
        $controller = new PayoutController();
        $results = $controller->processMonthlyPayouts();
        
        $this->info('Payout processing completed.');
        $this->table(
            ['Instructor', 'Status', 'Amount', 'Transfer ID'],
            collect($results)->map(function($result) {
                return [
                    $result['instructor'],
                    $result['status'],
                    $result['amount'] ?? 'N/A',
                    $result['transfer_id'] ?? 'N/A'
                ];
            })
        );
        
        // Log results
        Log::info('Monthly payouts processed', ['results' => $results]);
    }
}