<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    private function getTranzakToken()
{
    // Check if we have a valid token in cache
    $token = Cache::get('tranzak_auth_token');
    
    if ($token) {
        return $token;
    }
    
    // Generate new token
    $apiUrl = config('services.tranzak.base_url') . '/auth/token';
    
    Log::info('Attempting Tranzak authentication', [
        'url' => $apiUrl,
        'app_id' => config('services.tranzak.app_id'),
    ]);

    try {
        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'appId' => config('services.tranzak.app_id'),
                'appKey' => config('services.tranzak.api_key'),
            ]);

        Log::info('Tranzak auth response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            
            if ($responseData['success'] && isset($responseData['data']['token'])) {
                $token = $responseData['data']['token'];
                $expiresIn = $responseData['data']['expiresIn'] ?? 7200;
                
                // Cache the token for 90% of its validity period
                Cache::put('tranzak_auth_token', $token, $expiresIn * 0.9);
                
                Log::info('Tranzak token obtained successfully');
                return $token;
            } else {
                Log::error('Tranzak authentication failed in response', [
                    'error_code' => $responseData['errorCode'] ?? 'unknown',
                    'error_msg' => $responseData['errorMsg'] ?? 'Unknown error',
                    'success' => $responseData['success'] ?? false,
                ]);
            }
        } else {
            Log::error('Tranzak authentication HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Tranzak authentication exception: ' . $e->getMessage());
    }
    
    throw new \Exception('Failed to authenticate with Tranzak API. Please check your credentials.');
}

    public function initiatePayment(Request $request, Course $course)
    {
        try {
            // Check if user already purchased
            if ($request->user()->hasPurchased($course)) {
                return redirect()->back()->with('info', 'You already own this course.');
            }

            // Create a unique transaction ID
            $transactionId = 'TZ_' . time() . '_' . Str::random(8);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'amount' => $course->price,
                'currency' => 'USD',
                'status' => Payment::STATUS_PENDING,
                'transaction_id' => $transactionId,
            ]);

            // Get authentication token
            $token = $this->getTranzakToken();

            // Build API URL
            $apiUrl = config('services.tranzak.base_url') . '/xp021/v1/request/create';

            // Prepare Tranzak API request
            $payload = [
                'amount' => $course->price,
                'currencyCode' => 'USD',
                'description' => "Purchase of: " . $course->title,
                'mchTransactionRef' => $transactionId,
                'returnUrl' => route('payment.success'),
                'callbackUrl' => route('payment.webhook'),
            ];

            Log::info('Tranzak API request payload', $payload);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, $payload);

            Log::info('Tranzak API response', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            // Check response
            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success'] && isset($responseData['data']['links']['paymentAuthUrl'])) {
                    // Update payment with request ID if available
                    if (isset($responseData['data']['requestId'])) {
                        $payment->update(['tranzak_response' => ['requestId' => $responseData['data']['requestId']]]);
                    }
                    
                    return redirect()->away($responseData['data']['links']['paymentAuthUrl']);
                }
            }

            // Handle error
            $payment->update(['status' => Payment::STATUS_FAILED]);
            
            Log::error('Tranzak payment initiation failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            
            return redirect()->back()->with('error', 'Payment initiation failed. Please try again.');

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function handleSuccess(Request $request)
    {
        try {
            $requestId = $request->input('requestId');
            
            if (!$requestId) {
                return redirect()->route('courses.index')->with('error', 'Invalid payment response.');
            }

            // Verify payment with Tranzak
            $verification = $this->verifyPayment($requestId);
            
            if ($verification['success']) {
                $transactionRef = $verification['data']['mchTransactionRef'] ?? null;
                
                if (!$transactionRef) {
                    return redirect()->route('courses.index')
                        ->with('error', 'Payment verification failed. Missing transaction reference.');
                }
                
                $payment = Payment::where('transaction_id', $transactionRef)->first();
                
                if ($payment) {
                    if ($verification['data']['status'] === 'SUCCESSFUL') {
                        // Update payment status
                        $payment->markAsCompleted(
                            $verification['data']['payer']['paymentMethod'] ?? 'unknown',
                            $verification['data']
                        );
                        
                        // Create or update user course record
                        $userCourse = UserCourse::updateOrCreate(
                            [
                                'user_id' => $payment->user_id,
                                'course_id' => $payment->course_id
                            ],
                            [
                                'payment_id' => $payment->id,
                                'amount_paid' => $payment->amount,
                                'purchased_at' => now(),
                            ]
                        );
                        
                        // Redirect to success page with payment ID
                        return redirect()->route('payment.invoice', $payment->id)
                            ->with('success', 'Payment successful! You now have access to the course.');
                    } else {
                        $payment->update(['status' => Payment::STATUS_FAILED]);
                        return redirect()->route('courses.show', $payment->course)
                            ->with('error', 'Payment was not successful. Please try again.');
                    }
                }
            }
            
            return redirect()->route('courses.index')
                ->with('error', 'Payment verification failed. Please contact support.');
                
        } catch (\Exception $e) {
            Log::error('Payment success handling error: ' . $e->getMessage());
            return redirect()->route('courses.index')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    public function showInvoice(Payment $payment)
    {
        // Check if user is authorized to view this invoice
        if (auth()->id() !== $payment->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Check if payment is completed
        if (!$payment->isCompleted()) {
            return redirect()->back()->with('error', 'Invoice not available for incomplete payments.');
        }

        return view('payments.invoice', compact('payment'));
    }

    public function downloadInvoice(Payment $payment)
    {
        // Check if user is authorized to view this invoice
        if (auth()->id() !== $payment->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Check if payment is completed
        if (!$payment->isCompleted()) {
            return redirect()->back()->with('error', 'Invoice not available for incomplete payments.');
        }

        $pdf = PDF::loadView('payments.invoice-pdf', compact('payment'));
        
        return $pdf->download('invoice-' . $payment->transaction_id . '.pdf');
    }

    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('X-Tranzak-Signature');
            $data = json_decode($payload, true);
            
            Log::info('Tranzak webhook received', ['data' => $data, 'signature' => $signature]);
            
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($payload, $signature)) {
                Log::error('Invalid webhook signature', [
                    'received' => $signature,
                    'expected' => $this->generateSignature($payload)
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process webhook based on event type
            if (isset($data['eventType']) && $data['eventType'] === 'REQUEST.COMPLETED') {
                $resource = $data['resource'] ?? [];
                $transactionId = $resource['mchTransactionRef'] ?? null;
                
                if (!$transactionId) {
                    Log::error('Webhook missing transaction reference', ['data' => $data]);
                    return response()->json(['error' => 'Missing transaction reference'], 400);
                }
                
                $payment = Payment::where('transaction_id', $transactionId)->first();
                
                if ($payment) {
                    if ($resource['status'] === 'SUCCESSFUL') {
                        $payment->markAsCompleted(
                            $resource['payer']['paymentMethod'] ?? 'unknown',
                            $resource
                        );
                        
                        // Create user course record if it doesn't exist
                        if (!$payment->userCourse) {
                            UserCourse::create([
                                'user_id' => $payment->user_id,
                                'course_id' => $payment->course_id,
                                'payment_id' => $payment->id,
                                'amount_paid' => $payment->amount,
                                'purchased_at' => now(),
                            ]);
                        }
                    } else {
                        $payment->update(['status' => Payment::STATUS_FAILED]);
                    }
                    
                    Log::info('Webhook processed successfully', ['payment_id' => $payment->id, 'status' => $payment->status]);
                } else {
                    Log::error('Payment not found for webhook', ['transaction_id' => $transactionId]);
                }
            }

            return response()->json(['status' => 'processed']);
            
        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function handleCancel(Request $request)
    {
        $requestId = $request->input('requestId');
        
        if ($requestId) {
            // Find and update payment status to cancelled
            Payment::where('tranzak_response->requestId', $requestId)
                   ->update(['status' => Payment::STATUS_CANCELLED]);
        }
        
        return redirect()->route('courses.index')
            ->with('info', 'Payment was cancelled. You can try again whenever you\'re ready.');
    }

    private function verifyPayment($requestId)
    {
        try {
            $token = $this->getTranzakToken();
            $apiUrl = config('services.tranzak.base_url') . '/xp021/v1/request/details';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get($apiUrl, [
                'requestId' => $requestId
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => $responseData['success'],
                    'data' => $responseData['data'] ?? []
                ];
            }

            Log::error('Payment verification failed', [
                'requestId' => $requestId,
                'response' => $response->json()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
        }
        
        return ['success' => false, 'error' => 'Verification failed'];
    }

    private function verifyWebhookSignature($payload, $signature)
    {
        $secret = config('services.tranzak.webhook_secret');
        
        if (empty($secret)) {
            Log::warning('Webhook secret not configured. Signature verification skipped.');
            return true; // Skip verification if secret not set (for development)
        }
        
        $expectedSignature = $this->generateSignature($payload);
        return hash_equals($expectedSignature, $signature);
    }

    private function generateSignature($payload)
    {
        $secret = config('services.tranzak.webhook_secret');
        return hash_hmac('sha256', $payload, $secret);
    }
}