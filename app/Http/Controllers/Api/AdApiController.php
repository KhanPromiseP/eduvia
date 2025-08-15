<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\PageVisit;
use App\Models\AdTimeSpent;

class AdApiController extends Controller
{
    /**
     * Track page visit for analytics.
     */
    public function trackPageVisit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'url' => 'required|string',
                'referrer' => 'nullable|string',
                'timestamp' => 'required|integer',
            ]);

            // Store page visit data
            PageVisit::create([
                'url' => $request->url,
                'referrer' => $request->referrer,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'visited_at' => now(),
                'session_id' => $request->session()->getId(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Page visit tracking failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

  
/**
 * Track time spent on an ad.
 */
public function trackTimeSpent(Request $request): JsonResponse
{
    try {
        $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'time_spent' => 'required|numeric|min:0',
            'session_id' => 'nullable|string',
        ]);

        $adId = $request->input('ad_id');
        $timeSpent = $request->input('time_spent');
        $sessionId = $request->input('session_id') ?? $request->session()->getId();

        // Store or accumulate time spent per ad per session
        \App\Models\AdTimeSpent::updateOrCreate(
            [
                'ad_id' => $adId,
                'session_id' => $sessionId,
            ],
            [
                'time_spent' => \DB::raw("COALESCE(time_spent, 0) + {$timeSpent}"),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_tracked_at' => now(),
            ]
        );

        Log::info('Ad time spent tracked', [
            'ad_id' => $adId,
            'time_spent' => $timeSpent,
            'session_id' => $sessionId,
            'ip' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('Ad time spent tracking failed', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
        ]);

        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

}
