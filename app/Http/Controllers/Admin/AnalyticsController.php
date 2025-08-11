<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Record a new analytics event for an ad.
     *
     * Expected input:
     * - ad_id (in route)
     * - event_type: impression, click, or view
     * - duration (optional, for views)
     * - ip_address (optional)
     * - country (optional)
     * - device_type (optional)
     * - user_agent (optional)
     * - referrer (optional)
     * - value (optional)
     */
    public function store(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'event_type' => 'required|in:impression,click,view',
            'duration' => 'nullable|integer|min:0',
            'ip_address' => 'nullable|ip',
            'country' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|max:50',
            'user_agent' => 'nullable|string|max:1024',
            'referrer' => 'nullable|string|max:2048',
            'value' => 'nullable|integer|min:1',
        ]);

        $analytics = new Analytics([
            'ad_id' => $ad->id,
            'event_type' => $data['event_type'],
            'duration' => $data['duration'] ?? 0,
            'ip_address' => $data['ip_address'] ?? $request->ip(),
            'country' => $data['country'] ?? null,
            'device_type' => $data['device_type'] ?? null,
            'user_agent' => $data['user_agent'] ?? $request->userAgent(),
            'referrer' => $data['referrer'] ?? $request->headers->get('referer'),
            'value' => $data['value'] ?? 1,
        ]);

        $analytics->save();

        return response()->json(['message' => 'Analytics event recorded'], 201);
    }

    /**
     * Show summary analytics for a given ad.
     * Filters by date range, event_type, device_type, country, etc.
     */
    public function summary(Request $request, Ad $ad)
    {
        $query = Analytics::where('ad_id', $ad->id);

        // Apply filters safely
        if ($request->filled('event_type') && in_array($request->event_type, ['impression', 'click', 'view'])) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('start_date')) {
            try {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $query->where('created_at', '>=', $start);
            } catch (\Exception $e) {
                // Optionally log error or ignore invalid date
            }
        }

        if ($request->filled('end_date')) {
            try {
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->where('created_at', '<=', $end);
            } catch (\Exception $e) {
                // Optionally log error or ignore invalid date
            }
        }

        // Get total events count for filtered query
        $totalEvents = $query->count();

        // Average view duration for views only
        $avgDuration = (clone $query)->where('event_type', 'view')->avg('duration') ?? 0;

        // Group by event_type counts for the entire ad (ignore filters for summary counts)
        $eventsCount = Analytics::select('event_type')
            ->where('ad_id', $ad->id)
            ->groupBy('event_type')
            ->selectRaw('count(*) as count, event_type')
            ->pluck('count', 'event_type');

        // Top countries for the entire ad
        $topCountries = Analytics::select('country')
            ->where('ad_id', $ad->id)
            ->groupBy('country')
            ->selectRaw('count(*) as count, country')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top devices for the entire ad
        $topDevices = Analytics::select('device_type')
            ->where('ad_id', $ad->id)
            ->groupBy('device_type')
            ->selectRaw('count(*) as count, device_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.analytics.summary', compact(
            'ad', 'totalEvents', 'avgDuration', 'eventsCount', 'topCountries', 'topDevices'
        ));
    }

    /**
     * List analytics events with flexible filtering and pagination.
     */
    public function index(Request $request, Ad $ad)
    {
        $query = Analytics::where('ad_id', $ad->id);

        if ($request->filled('event_type') && in_array($request->event_type, ['impression', 'click', 'view'])) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('start_date')) {
            try {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $query->where('created_at', '>=', $start);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        if ($request->filled('end_date')) {
            try {
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->where('created_at', '<=', $end);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        $analytics = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.analytics.index', compact('ad', 'analytics'));
    }
}
