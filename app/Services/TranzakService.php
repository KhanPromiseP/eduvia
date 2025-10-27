<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranzakService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.tranzak.base_url');
        $this->apiKey = config('services.tranzak.api_key');
        $this->apiSecret = config('services.tranzak.webhook_secret');
    }

    /**
     * Get Tranzak API token with caching
     */
    public function getTranzakToken()
    {
        return Cache::remember('tranzak_token', 3600, function () { // Cache for 1 hour
            try {
                $response = Http::timeout(30)->post($this->baseUrl . '/auth/token', [
                    'apiKey' => $this->apiKey,
                    'apiSecret' => $this->apiSecret
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['success'] && isset($data['data']['accessToken'])) {
                        return $data['data']['accessToken'];
                    }
                }

                Log::error('Tranzak token API error: ' . $response->body());
                throw new \Exception('Failed to get Tranzak token: ' . ($data['message'] ?? 'Unknown error'));

            } catch (\Exception $e) {
                Log::error('Tranzak token error: ' . $e->getMessage());
                throw new \Exception('Tranzak service unavailable: ' . $e->getMessage());
            }
        });
    }

    /**
     * Verify mobile money account
     */
    public function verifyMobileMoneyAccount($data)
    {
        try {
            $token = $this->getTranzakToken();
            $apiUrl = $this->baseUrl . '/xp021/v1/name-verification/create';
            
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'accountHolderId' => $data['account_number'],
                'customTransactionId' => 'verify_' . time()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $verified = $result['success'] && 
                           $result['data']['status'] === 'COMPLETED' &&
                           !empty($result['data']['verifiedName']);

                return [
                    'verified' => $verified,
                    'verified_name' => $result['data']['verifiedName'] ?? null,
                    'operator' => $result['data']['operatorName'] ?? null,
                    'request_id' => $result['data']['requestId'] ?? null,
                    'verified_at' => now()->toISOString()
                ];
            }

            Log::error('Mobile money verification API error: ' . $response->body());
            return ['verified' => false, 'error' => 'API verification failed'];

        } catch (\Exception $e) {
            Log::error('Mobile money verification failed: ' . $e->getMessage());
            return ['verified' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify bank account
     */
    public function verifyBankAccount($data)
    {
        try {
            $token = $this->getTranzakToken();
            // Adjust this endpoint based on Tranzak's bank verification API
            $apiUrl = $this->baseUrl . '/xp021/v1/bank-verification/create';
            
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'accountNumber' => $data['account_number'],
                'bankCode' => $this->getBankCode($data['operator']),
                'customTransactionId' => 'bank_verify_' . time()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $verified = $result['success'] && 
                           $result['data']['status'] === 'COMPLETED' &&
                           !empty($result['data']['accountName']);

                return [
                    'verified' => $verified,
                    'verified_name' => $result['data']['accountName'] ?? null,
                    'bank_name' => $data['operator'],
                    'request_id' => $result['data']['requestId'] ?? null,
                    'verified_at' => now()->toISOString()
                ];
            }

            Log::error('Bank verification API error: ' . $response->body());
            return ['verified' => false, 'error' => 'API verification failed'];

        } catch (\Exception $e) {
            Log::error('Bank verification failed: ' . $e->getMessage());
            return ['verified' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Process payout to instructor
     */
    public function processPayout($payoutData)
    {
        try {
            $token = $this->getTranzakToken();
            
            if ($payoutData['payout_method'] === 'mobile_money') {
                return $this->processMobileMoneyPayout($payoutData, $token);
            } elseif ($payoutData['payout_method'] === 'bank_account') {
                return $this->processBankTransferPayout($payoutData, $token);
            } else {
                return $this->processWalletPayout($payoutData, $token);
            }

        } catch (\Exception $e) {
            Log::error('Payout processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process mobile money payout
     */
    private function processMobileMoneyPayout($payoutData, $token)
    {
        $apiUrl = $this->baseUrl . '/xp021/v1/transfer/to-mobile-wallet';
        
        $payload = [
            'amount' => $payoutData['amount'],
            'currencyCode' => $payoutData['currency'] ?? 'XAF',
            'description' => $payoutData['description'] ?? "Monthly payout",
            'customTransactionRef' => $payoutData['transaction_ref'] ?? 'payout_' . time(),
            'payeeAccountId' => $payoutData['account_number'],
            'payeeAccountName' => $payoutData['account_name'],
            'payeeNote' => $payoutData['note'] ?? 'Instructor earnings payout',
            'verifyPayeeName' => true
        ];

        $response = Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, $payload);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'transfer_id' => $result['data']['transferId'] ?? null,
                    'transaction_ref' => $result['data']['transactionRef'] ?? null,
                    'status' => $result['data']['status'] ?? 'COMPLETED'
                ];
            }
        }

        Log::error('Mobile money payout API error: ' . $response->body());
        throw new \Exception('Payout API call failed: ' . ($response->body() ?? 'Unknown error'));
    }

    /**
     * Process bank transfer payout
     */
    private function processBankTransferPayout($payoutData, $token)
    {
        $apiUrl = $this->baseUrl . '/xp021/v1/transfer/to-bank-account';
        
        $payload = [
            'amount' => $payoutData['amount'],
            'currencyCode' => $payoutData['currency'] ?? 'XAF',
            'description' => $payoutData['description'] ?? "Monthly payout",
            'customTransactionRef' => $payoutData['transaction_ref'] ?? 'payout_' . time(),
            'accountNumber' => $payoutData['account_number'],
            'accountName' => $payoutData['account_name'],
            'bankCode' => $this->getBankCode($payoutData['operator']),
            'payeeNote' => $payoutData['note'] ?? 'Instructor earnings payout'
        ];

        $response = Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, $payload);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'transfer_id' => $result['data']['transferId'] ?? null,
                    'transaction_ref' => $result['data']['transactionRef'] ?? null,
                    'status' => $result['data']['status'] ?? 'PROCESSING'
                ];
            }
        }

        Log::error('Bank transfer payout API error: ' . $response->body());
        throw new \Exception('Bank transfer API call failed: ' . ($response->body() ?? 'Unknown error'));
    }

    /**
     * Process wallet payout (internal transfer)
     */
    private function processWalletPayout($payoutData, $token)
    {
        // For wallet transfers, you might handle this internally
        // or use Tranzak's wallet transfer API if available
        return [
            'success' => true,
            'transfer_id' => 'wallet_' . time(),
            'status' => 'COMPLETED',
            'internal' => true
        ];
    }

    /**
     * Get bank code for Tranzak API
     */
    private function getBankCode($bankName)
    {
        $bankCodes = [
            'Afriland First Bank' => 'AFB',
            'BICEC' => 'BICEC',
            'Société Générale' => 'SGC',
            'UBA' => 'UBA',
            'ECOBANK' => 'ECO',
            // Add more bank codes as needed
        ];

        return $bankCodes[$bankName] ?? 'OTHER';
    }

    /**
     * Clear cached token (useful for testing or token issues)
     */
    public function clearCachedToken()
    {
        Cache::forget('tranzak_token');
    }
}