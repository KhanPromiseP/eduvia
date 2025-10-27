<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PaymentController;

class CheckPayoutBalance extends Command
{
    protected $signature = 'payouts:check-balance';
    protected $description = 'Check payout account balance';

    public function handle()
    {
        try {
            $token = app()->make(PaymentController::class)->getTranzakToken();
            $apiUrl = config('services.tranzak.base_url') . '/xp021/v1/account/payout-account-details';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post($apiUrl);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    $balance = $data['data']['totalBalance'] ?? 0;
                    $currency = $data['data']['currencyCode'] ?? 'XAF';
                    
                    $this->info("Payout account balance: {$balance} {$currency}");
                    
                    // Log balance for monitoring
                    Log::info('Payout account balance checked', [
                        'balance' => $balance,
                        'currency' => $currency
                    ]);
                    
                    // You can add alert logic here if balance is low
                    if ($balance < 100000) { // Example threshold: 100,000 XAF
                        $this->warn('Payout account balance is low!');
                        // Send notification to admin
                    }
                    
                } else {
                    $this->error('Failed to get balance: ' . ($data['errorMsg'] ?? 'Unknown error'));
                }
            } else {
                $this->error('HTTP error checking balance: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error('Balance check failed: ' . $e->getMessage());
            Log::error('Payout balance check failed: ' . $e->getMessage());
        }
    }
}