<?php

namespace App\Http\Controllers;

use App\Models\InstructorPayout;
use App\Models\InstructorEarning;
use App\Models\Instructor;
use App\Services\TranzakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    private $tranzakService;

    public function __construct(TranzakService $tranzakService)
    {
        $this->tranzakService = $tranzakService;
    }

    public function showPayoutSetup(Request $request)
    {
        $instructor = $request->user()->instructor;
        $payout = $instructor->payouts ?? new InstructorPayout();
        $totalEarnings = $instructor->earnings()->processed()->sum('amount');
        $pendingEarnings = $instructor->earnings()->forPayout()->sum('amount');
        
        return view('instructor.payout-setup', compact('payout', 'totalEarnings', 'pendingEarnings'));
    }

    public function setupPayout(Request $request)
    {
        $request->validate([
            'payout_method' => 'required|in:mobile_money,bank_account,tranzak_wallet',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'operator' => 'required_if:payout_method,mobile_money,bank_account',
            'auto_payout' => 'boolean',
            'payout_threshold' => 'numeric|min:0'
        ]);

        $instructor = $request->user()->instructor;
        
        try {
            // Verify account with Tranzak
            $verificationResult = $this->verifyPayoutAccount($request->all());
            
            $payoutData = [
                'instructor_id' => $instructor->id,
                'payout_method' => $request->payout_method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'operator' => $request->operator,
                'auto_payout' => $request->boolean('auto_payout'),
                'payout_threshold' => $request->payout_threshold ?? 0,
                'verification_data' => $verificationResult,
                'is_verified' => $verificationResult['verified'] ?? false,
            ];
            
            // Update or create payout settings
            $payout = InstructorPayout::updateOrCreate(
                ['instructor_id' => $instructor->id],
                $payoutData
            );
            
            if ($payout->is_verified) {
                return redirect()->route('instructor.payout.setup')
                    ->with('success', 'Payout account verified and setup successfully!');
            } else {
                return redirect()->route('instructor.payout.setup')
                    ->with('warning', 'Account setup completed but requires manual verification. We will notify you once verified.');
            }
            
        } catch (\Exception $e) {
            Log::error('Payout setup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to setup payout account: ' . $e->getMessage());
        }
    }

    private function verifyPayoutAccount($data)
    {
        // For mobile money, use Tranzak's name verification API
        if ($data['payout_method'] === 'mobile_money') {
            return $this->tranzakService->verifyMobileMoneyAccount($data);
        }
        
        // For bank accounts, use bank verification
        if ($data['payout_method'] === 'bank_account') {
            return $this->tranzakService->verifyBankAccount($data);
        }
        
        // For Tranzak wallet, no verification needed
        return ['verified' => true, 'method' => 'automatic'];
    }

    // Monthly Payout Processing Command
    public function processMonthlyPayouts()
    {
        $instructors = Instructor::with(['payouts', 'earnings' => function($query) {
            $query->forPayout();
        }])->whereHas('payouts', function($query) {
            $query->where('is_verified', true)
                  ->where('auto_payout', true);
        })->get();

        $results = [];
        
        foreach ($instructors as $instructor) {
            $pendingAmount = $instructor->earnings->sum('amount');
            $payout = $instructor->payouts;
            
            // Check if meets payout threshold
            if ($pendingAmount >= $payout->payout_threshold) {
                try {
                    $result = $this->processInstructorPayout($instructor, $pendingAmount);
                    $results[] = $result;
                } catch (\Exception $e) {
                    Log::error("Payout failed for instructor {$instructor->id}: " . $e->getMessage());
                    $results[] = [
                        'instructor' => $instructor->user->name,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $results;
    }

    private function processInstructorPayout($instructor, $amount)
    {
        $payout = $instructor->payouts;
        $earnings = $instructor->earnings->pluck('id');
        
        try {
            $payoutData = [
                'payout_method' => $payout->payout_method,
                'amount' => $amount,
                'currency' => $payout->currency ?? 'XAF',
                'account_number' => $payout->account_number,
                'account_name' => $payout->account_name,
                'operator' => $payout->operator,
                'description' => "Monthly payout for " . now()->format('F Y'),
                'transaction_ref' => 'payout_' . $instructor->id . '_' . time(),
                'note' => 'Instructor earnings payout'
            ];
            
            $result = $this->tranzakService->processPayout($payoutData);
            
            if ($result['success']) {
                // Mark earnings as paid out
                InstructorEarning::whereIn('id', $earnings)->update([
                    'status' => InstructorEarning::STATUS_PAID_OUT,
                    'paid_out_at' => now()
                ]);
                
                Log::info("Payout processed for instructor {$instructor->id}", [
                    'amount' => $amount,
                    'transfer_id' => $result['transfer_id'] ?? null
                ]);
                
                return [
                    'instructor' => $instructor->user->name,
                    'status' => 'success',
                    'amount' => $amount,
                    'transfer_id' => $result['transfer_id'] ?? null
                ];
            }
            
            throw new \Exception('Payout processing failed: ' . ($result['error'] ?? 'Unknown error'));
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
}