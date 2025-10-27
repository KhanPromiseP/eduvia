<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    public function requestRefund(Request $request, Payment $payment)
    {
        // Check if user can request refund
        if ($payment->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if within refund period (30 days)
        if ($payment->completed_at->diffInDays(now()) > 30) {
            return redirect()->back()->with('error', 'Refund period has expired (30 days).');
        }
        
        // Check if already has active refund request
        $existingRequest = RefundRequest::where('payment_id', $payment->id)
            ->whereIn('status', [RefundRequest::STATUS_PENDING, RefundRequest::STATUS_APPROVED])
            ->first();
            
        if ($existingRequest) {
            return redirect()->back()->with('info', 'You already have an active refund request for this payment.');
        }

        return view('refunds.request', compact('payment'));
    }

    public function submitRefund(Request $request, Payment $payment)
    {
        $request->validate([
            'reason_code' => 'required|integer|in:1,2,3,4,5,6,7,8',
            'reason' => 'required|string|max:1000'
        ]);

        $refundRequest = RefundRequest::create([
            'user_id' => $request->user()->id,
            'payment_id' => $payment->id,
            'course_id' => $payment->course_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'reason' => $request->reason,
            'reason_code' => $request->reason_code,
            'status' => RefundRequest::STATUS_PENDING
        ]);

        // Notify admin about new refund request
        // You can implement notification system here

        return redirect()->route('user.purchases')
            ->with('success', 'Refund request submitted successfully. We will review it within 3-5 business days.');
    }

    public function processRefund(RefundRequest $refundRequest)
    {
        if (!$refundRequest->canBeProcessed()) {
            return redirect()->back()->with('error', 'Refund cannot be processed in current status.');
        }

        try {
            $token = app()->make(PaymentController::class)->getTranzakToken();
            $apiUrl = config('services.tranzak.base_url') . '/xp021/v1/refund/create';
            
            $payload = [
                'refundedTransactionId' => $refundRequest->payment->transaction_id,
                'amount' => $refundRequest->amount,
                'reasonCode' => $refundRequest->reason_code,
                'mchTransactionRef' => 'refund_' . $refundRequest->id,
                'note' => $refundRequest->reason
            ];
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $payload);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success']) {
                    $refundRequest->update([
                        'status' => RefundRequest::STATUS_PROCESSED,
                        'refund_id' => $result['data']['refundId'] ?? null,
                        'refunded_at' => now()
                    ]);
                    
                    // Reverse instructor earnings if already paid
                    $this->reverseInstructorEarnings($refundRequest);
                    
                    Log::info('Refund processed successfully', [
                        'refund_request_id' => $refundRequest->id,
                        'refund_id' => $result['data']['refundId'] ?? null
                    ]);
                    
                    return redirect()->back()->with('success', 'Refund processed successfully.');
                }
            }
            
            throw new \Exception('Refund API call failed: ' . ($response->body() ?? 'Unknown error'));
            
        } catch (\Exception $e) {
            Log::error('Refund processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    private function reverseInstructorEarnings(RefundRequest $refundRequest)
    {
        // Find and reverse the instructor earnings for this payment
        $earning = InstructorEarning::where('payment_id', $refundRequest->payment_id)->first();
        
        if ($earning) {
            // You can create a negative earning record or mark as reversed
            $earning->update(['status' => 'reversed']);
            
            Log::info('Instructor earnings reversed for refund', [
                'refund_request_id' => $refundRequest->id,
                'earning_id' => $earning->id
            ]);
        }
    }
}