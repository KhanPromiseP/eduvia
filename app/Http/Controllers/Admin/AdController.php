<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdView;
use App\Models\AdClick;
use App\Models\AdTimeSpent;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;  // Add this line

class AdController extends Controller
{
public function index(Request $request)
{
    // Start with the admin scope that shows all ads
    $ads = Ad::forAdmin();

    // Apply filters - but make status and date filters optional for admin
    if ($request->has('status') && $status = $request->status) {
        $ads->where('is_active', $status === 'active');
    }

    if ($request->has('search') && $search = $request->search) {
        $ads->where('title', 'like', "%{$search}%");
    }

    if ($request->has('weight') && $weight = $request->weight) {
        $ads->where('weight', $weight);
    }

    if ($request->has('is_random') && $request->is_random) {
        $ads->where('is_random', true);
    }

    // Date filters - but only apply if explicitly requested
    if ($request->has('start_date') && $startDate = $request->start_date) {
        $ads->where('start_at', '>=', $startDate);
    }

    if ($request->has('end_date') && $endDate = $request->end_date) {
        $ads->where('end_at', '<=', $endDate);
    }

    if ($request->has('type') && $type = $request->type) {
        if ($type !== 'all') {
            $ads->where('type', $type);
        }
    }

    if ($request->has('placement') && $placement = $request->placement) {
        if ($placement !== 'any') {
            $ads->where('placement', $placement);
        }
    }

    // Paginate after all filters
    $ads = $ads->paginate(15)->withQueryString();

    // Filter options
    $placements = [
        'header'       => 'Header',
        'sidebar'      => 'Sidebar',
        'footer'       => 'Footer',
        'in-content'   => 'In Content',
        'floating'     => 'Floating',
        'popup'        => 'Popup',
        'interstitial' => 'Interstitial',
        'any'          => 'Any Placement',
    ];

    $adTypes = [
        'banner'       => 'Banner',
        'video'        => 'Video',
        'popup'        => 'Popup',
        'interstitial' => 'Interstitial',
        'js'           => 'JavaScript',
        'persistent'   => 'Persistent',
        'image'        => 'Image',
        'all'          => 'All Types',
    ];

    return view('admin.ads.index', compact('ads', 'placements', 'adTypes'));
}



    /**
     * Show the form for creating a new ad.
     */
    public function create()
    {
        $products = Product::select('id', 'title')->get();
        $users = User::select('id', 'name', 'email')->get();
        
        $adTypes = [
            'image' => 'Image Ad',
            'video' => 'Video Ad',
            'banner' => 'Banner Ad',
            'js' => 'JavaScript Ad',
            'popup' => 'Popup Ad',
            'persistent' => 'Persistent Ad',
            'interstitial' => 'Interstitial Ad'
        ];

        $placements = [
            'header' => 'Header',
            'sidebar' => 'Sidebar',
            'footer' => 'Footer',
            'in-content' => 'In Content',
            'floating' => 'Floating',
            'popup' => 'Popup',
            'interstitial' => 'Interstitial'
        ];

        return view('admin.ads.create', compact('products', 'users', 'adTypes', 'placements'));
    }

    /**
     * Store a newly created ad in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateAd($request);

        // Normalize boolean checkbox values: presence means true, absence means false
        $data['is_active'] = $request->has('is_active');
        $data['is_random'] = $request->has('is_random');

        // Decode targeting JSON string if provided
        if (!empty($data['targeting']) && is_string($data['targeting'])) {
            $decoded = json_decode($data['targeting'], true);
            $data['targeting'] = $decoded ?: null;
        }

        // Set default dates if not provided
        if (empty($data['start_at'])) {
            $data['start_at'] = now();
        }

        // Create the ad
        $ad = Ad::create($data);

        // Clear ad cache
        $this->clearAdCache();

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad created successfully.');
    }


    /**
     * Get total time spent on an ad (all sessions combined).
     */
    protected function getTotalTimeSpent(Ad $ad): float
    {
        // Sum all time_spent entries for this ad
        $totalTime = AdTimeSpent::where('ad_id', $ad->id)
            ->sum('time_spent');

        return round($totalTime, 2); // seconds
    }
 

    /**
     * Display the specified ad with analytics.
     */
 public function show(Ad $ad)
{
    // Basic counts
    $ad->loadCount(['views', 'clicks', 'timeSpent']);

    // Periods to calculate stats
    $periods = [
        'daily' => now()->subDay(),
        'weekly' => now()->subDays(7),
        'monthly' => now()->subDays(30),
    ];

    $analytics = [];

    foreach ($periods as $key => $startDate) {
        $endDate = now();

        // Current period totals
        $currentViews = $ad->views()->whereBetween('viewed_at', [$startDate, $endDate])->count();
        $currentClicks = $ad->clicks()->whereBetween('clicked_at', [$startDate, $endDate])->count();

        // Previous period totals for trend calculation
        $previousStart = $startDate->copy()->sub($endDate->diff($startDate));
        $previousEnd = $startDate;

        $previousViews = $ad->views()->whereBetween('viewed_at', [$previousStart, $previousEnd])->count();
        $previousClicks = $ad->clicks()->whereBetween('clicked_at', [$previousStart, $previousEnd])->count();

        // Calculate trends
        $viewsChange = $previousViews > 0 ? round((($currentViews - $previousViews) / $previousViews) * 100, 2) : 0;
        $clicksChange = $previousClicks > 0 ? round((($currentClicks - $previousClicks) / $previousClicks) * 100, 2) : 0;

        $analytics[$key] = [
            'total_views' => $currentViews,
            'total_clicks' => $currentClicks,
            'views_change' => $viewsChange,
            'clicks_change' => $clicksChange,
            'ctr' => $this->calculateCTR($ad, $startDate, $endDate),
            'engagement_rate' => $this->calculateEngagementRate($ad, $startDate, $endDate),
            'time_spent' => $this->getTimeSpentStatsByPeriod($ad, $startDate, $endDate),
        ];
    }

    // Existing detailed stats for graphs
    $analytics['daily_stats'] = $this->getDailyStats($ad, now()->subDays(30), now());
    $analytics['hourly_stats'] = $this->getHourlyStats($ad, now()->subDays(30), now());
    $analytics['weekday_stats'] = $this->getWeekdayStats($ad, now()->subDays(30), now());
    $analytics['device_stats'] = $this->getDeviceStats($ad, now()->subDays(30), now());
    $analytics['geo_stats'] = $this->getGeoStats($ad, now()->subDays(30), now());
    $analytics['top_referrers'] = $this->getTopReferrers($ad, now()->subDays(30), now());

    // First/last events
    $analytics['first_impression'] = $ad->views()->orderBy('viewed_at')->first()?->viewed_at;
    $analytics['last_impression'] = $ad->views()->orderByDesc('viewed_at')->first()?->viewed_at;
    $analytics['first_click'] = $ad->clicks()->orderBy('clicked_at')->first()?->clicked_at;
    $analytics['last_click'] = $ad->clicks()->orderByDesc('clicked_at')->first()?->clicked_at;

    $analytics['cost_per_click'] = $this->calculateCostPerClick($ad);

    return view('admin.ads.show', compact('ad', 'analytics'));
}



protected function getTimeSpentStatsByPeriod(Ad $ad, $startDate, $endDate)
{
    $total = $ad->timeSpent()->whereBetween('last_tracked_at', [$startDate, $endDate])->sum('time_spent');
    $views = $ad->views()->whereBetween('viewed_at', [$startDate, $endDate])->count();
    $average = $views > 0 ? $total / $views : 0;

    return [
        'total' => round($total, 2),
        'average' => round($average, 2),
    ];
}




// Helper methods for analytics:

protected function getDailyStats(Ad $ad, $startDate, $endDate)
{
    return $ad->views()
        ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->date => $item->views];
        });
}

protected function getHourlyStats(Ad $ad, $startDate, $endDate)
{
    $stats = $ad->views()
        ->selectRaw('HOUR(viewed_at) as hour, COUNT(*) as count')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();
    
    $hourly = array_fill(0, 24, 0);
    foreach ($stats as $stat) {
        $hourly[$stat->hour] = $stat->count;
    }
    
    return $hourly;
}

protected function getWeekdayStats(Ad $ad, $startDate, $endDate)
{
    return $ad->views()
        ->selectRaw('DAYOFWEEK(viewed_at) as weekday, COUNT(*) as count')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('weekday')
        ->orderBy('weekday')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->weekday => $item->count];
        });
}

private function getDeviceStats($ad, $startDate, $endDate)
{
    // Get views grouped by device type
    $views = AdView::selectRaw("
            CASE
                WHEN viewport_width <= 768 THEN 'Mobile'
                WHEN viewport_width <= 1024 THEN 'Tablet'
                ELSE 'Desktop'
            END as device_type
        ")
        ->where('ad_id', $ad->id)
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->get();

    // Group by device type
    $deviceStats = $views->groupBy('device_type')->map(function($group, $deviceType) use ($ad, $startDate, $endDate) {
        $viewsCount = $group->count();

        // Count clicks for this device by matching ad_id and session_id
        $sessionIds = $group->pluck('session_id')->toArray();
        $clicksCount = \App\Models\AdClick::where('ad_id', $ad->id)
                        ->whereIn('session_id', $sessionIds)
                        ->whereBetween('clicked_at', [$startDate, $endDate])
                        ->count();

        return [
            'views' => $viewsCount,
            'clicks' => $clicksCount,
            'ctr' => $viewsCount > 0 ? round(($clicksCount / $viewsCount) * 100, 2) : 0,
        ];
    });

    // Determine best device by CTR then clicks
    $bestDevice = $deviceStats->sortByDesc(function($d) {
        return $d['ctr'] * 1000 + $d['clicks']; // scoring
    })->keys()->first();

    return [
        'stats' => $deviceStats,
        'best_device' => $bestDevice,
    ];
}


protected function getGeoStats(Ad $ad, $startDate, $endDate)
{
    return $ad->views()
        ->selectRaw('country, city, COUNT(*) as count')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->whereNotNull('country')
        ->groupBy('country', 'city')
        ->orderByDesc('count')
        ->limit(10)
        ->get();
}

protected function getTimeSpentStats(Ad $ad)
{
    $total = $ad->timeSpent()->sum('time_spent');
    $average = $ad->views_count > 0 ? $total / $ad->views_count : 0;
    
    return [
        'total' => round($total, 2), // Round to 2 decimal places
        'average' => round($average, 2),
    ];
}

protected function calculateCTR(Ad $ad, $startDate, $endDate)
{
    $views = $ad->views()->whereBetween('viewed_at', [$startDate, $endDate])->count();
    $clicks = $ad->clicks()->whereBetween('clicked_at', [$startDate, $endDate])->count();
    
    return $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
}

protected function calculateEngagementRate(Ad $ad, $startDate, $endDate)
{
    $views = $ad->views()->whereBetween('viewed_at', [$startDate, $endDate])->count();
    $engaged = $ad->timeSpent()
        ->where('time_spent', '>', 5) // Consider >5 seconds as engaged
        ->whereBetween('last_tracked_at', [$startDate, $endDate])
        ->count();
    
    return $views > 0 ? round(($engaged / $views) * 100, 2) : 0;
}

protected function calculateCostPerClick(Ad $ad)
{
    if (!$ad->budget || $ad->clicks_count == 0) {
        return 0;
    }
    
    return round($ad->budget / $ad->clicks_count, 4);
}

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(Ad $ad)
    {
        $products = Product::select('id', 'title')->get();
        $users = User::select('id', 'name', 'email')->get();
        
        $adTypes = [
            'image' => 'Image Ad',
            'video' => 'Video Ad',
            'banner' => 'Banner Ad',
            'js' => 'JavaScript Ad',
            'popup' => 'Popup Ad',
            'persistent' => 'Persistent Ad',
            'interstitial' => 'Interstitial Ad'
        ];

        $placements = [
            'header' => 'Header',
            'sidebar' => 'Sidebar',
            'footer' => 'Footer',
            'in-content' => 'In Content',
            'floating' => 'Floating',
            'popup' => 'Popup',
            'interstitial' => 'Interstitial'
        ];

        return view('admin.ads.edit', compact('ad', 'products', 'users', 'adTypes', 'placements'));
    }






    

    /**
     * Update the specified ad in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        $data = $this->validateAd($request, $ad->id);

        // Normalize boolean checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_random'] = $request->has('is_random');

        // Decode targeting JSON string if provided
        if (!empty($data['targeting']) && is_string($data['targeting'])) {
            $decoded = json_decode($data['targeting'], true);
            $data['targeting'] = $decoded ?: null;
        }

        $ad->update($data);

        // Clear ad cache
        $this->clearAdCache();

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad updated successfully.');
    }

    /**
     * Remove the specified ad from storage.
     */
    public function destroy(Ad $ad)
    {
        $ad->delete();

        // Clear ad cache
        $this->clearAdCache();

        return redirect()->route('admin.ads.index')
            ->with('success', 'Ad deleted successfully.');
    }

    /**
     * Get ads for a specific placement with caching and targeting.
     */
    public function getAdsForPlacement(Request $request, string $placement): JsonResponse
    {
        $cacheKey = "ads.placement.{$placement}." . md5($request->userAgent() . $request->ip());
        
        $ads = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($placement, $request) {
            $query = Ad::active()
                ->currentlyRunning()
                ->where(function ($q) use ($placement) {
                    $q->where('placement', $placement)
                      ->orWhere('placement', 'any')
                      ->orWhereNull('placement');
                });

            // Apply targeting filters
            $this->applyTargetingFilters($query, $request);

            // Get ads with weight-based random selection if is_random is true
            $ads = $query->get();
            
            if ($ads->where('is_random', true)->count() > 0) {
                $randomAds = $ads->where('is_random', true);
                $staticAds = $ads->where('is_random', false);
                
                // Shuffle random ads and take a limited number
                $selectedRandom = $randomAds->shuffle()->take(2);
                $ads = $staticAds->merge($selectedRandom);
            }

            return $ads->take(5); // Limit to 5 ads per placement
        });

        return response()->json([
            'success' => true,
            'ads' => $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'type' => $ad->type,
                    'content' => $ad->content,
                    'link' => $ad->link,
                    'targeting' => $ad->targeting,
                ];
            }),
        ]);
    }

// AdController.php

public function trackView(Request $request): JsonResponse
{
    try {
        // Log the incoming request for debugging
        Log::debug('TrackView Request:', [
            'all_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer')
        ]);

        $validated = $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'timestamp' => 'required|integer',
            'url' => 'required|url',
            'viewport' => 'required|array',
            'viewport.width' => 'required|integer',
            'viewport.height' => 'required|integer',
            'session_id' => 'required|string',
        ]);

        // Log validated data
        Log::debug('TrackView Validated:', $validated);

        // Get geo data from IP
        $geoData = $this->getGeoData($request->ip());

        AdView::create([
            'ad_id' => $validated['ad_id'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'url' => $validated['url'],
            'viewport_width' => $validated['viewport']['width'],
            'viewport_height' => $validated['viewport']['height'],
            'viewed_at' => Carbon::createFromTimestampMs($validated['timestamp']),
            'session_id' => $validated['session_id'],
            'country' => $geoData['country'] ?? null,
            'city' => $geoData['city'] ?? null,
        ]);

        Log::debug('TrackView Success: Record created');
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('Ad view tracking failed', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function trackClick(Request $request): JsonResponse
{
    try {
        $validated = $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'timestamp' => 'required|integer',
            'url' => 'required|url',
            'target_url' => 'nullable|url', // Explicitly allow null
            'session_id' => 'required|string',
            'placement' => 'sometimes|string' // Added placement if needed
        ]);

        // Get geo data
        $geoData = [];
        try {
            $geoData = Http::get("http://ip-api.com/json/{$request->ip()}?fields=country,city")
                         ->json();
        } catch (\Exception $e) {
            Log::warning('Geo IP lookup failed', ['error' => $e->getMessage()]);
        }

        // Create click record with fallback for target_url
        AdClick::create([
            'ad_id' => $validated['ad_id'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $validated['url'],
            'target_url' => $validated['target_url'] ?? $validated['url'], // Fallback to referrer if null
            'clicked_at' => Carbon::createFromTimestampMs($validated['timestamp']),
            'session_id' => $validated['session_id'],
            'country' => $geoData['country'] ?? null,
            'city' => $geoData['city'] ?? null,
        ]);

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Ad click tracking failed', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['success' => false], 500);
    }
}

public function trackTimeSpent(Request $request): JsonResponse
{
    DB::beginTransaction();
    
    try {
        Log::debug('TimeSpent Request Data:', $request->all());

        $validated = $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'session_id' => 'required|string|max:255',
            'time_spent' => 'required|numeric|min:0|max:999999.99',
            'last_tracked_at' => 'required|integer|min:0',
            'placement' => 'sometimes|string|max:50'
        ]);

        Log::debug('Validated Data:', $validated);

        // Convert and format all data before saving
        $timeSpent = (float)number_format($validated['time_spent'], 2, '.', '');
        $timestamp = $validated['last_tracked_at'];
        
        Log::debug('Timestamp processing:', [
            'timestamp' => $timestamp,
            'timestamp_length' => strlen((string)$timestamp),
            'timestamp_type' => gettype($timestamp)
        ]);

        $lastTrackedAt = (strlen((string)$timestamp) === 13) 
            ? Carbon::createFromTimestampMs($timestamp)
            : Carbon::createFromTimestamp($timestamp);

        Log::debug('Carbon conversion:', [
            'last_tracked_at' => $lastTrackedAt,
            'last_tracked_at_format' => $lastTrackedAt->format('Y-m-d H:i:s')
        ]);

        // Use updateOrCreate but with pre-formatted values
        $record = AdTimeSpent::updateOrCreate(
            [
                'ad_id' => $validated['ad_id'],
                'session_id' => $validated['session_id']
            ],
            [
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 512),
                'time_spent' => $timeSpent,
                'last_tracked_at' => $lastTrackedAt,
                'placement' => $validated['placement'] ?? null
            ]
        );

        Log::debug('Database operation successful:', ['record_id' => $record->id]);

        DB::commit();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Time spent tracking failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Tracking failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

protected function getGeoData($ip): array
{
    // Use a geo IP service or local database
    // Example with freegeoip.app (rate limited)
    try {
        $response = Http::get("http://ip-api.com/json/{$ip}?fields=country,city");
        return $response->json();
    } catch (\Exception $e) {
        Log::error('Geo IP lookup failed', ['error' => $e->getMessage()]);
        return [];
    }
}

    /**
     * Track ad close.
     */
    public function trackClose(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ad_id' => 'required|exists:ads,id',
                'timestamp' => 'required|integer',
            ]);

            // You can create an AdClose model or add to existing tracking
            Log::info('Ad closed', [
                'ad_id' => $request->ad_id,
                'timestamp' => $request->timestamp,
                'ip' => $request->ip(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Ad close tracking failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get ad performance analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        
        $analytics = [
            'overview' => [
                'total_ads' => Ad::count(),
                'active_ads' => Ad::active()->count(),
                'total_views' => AdView::whereBetween('viewed_at', [$dateFrom, $dateTo])->count(),
                'total_clicks' => AdClick::whereBetween('clicked_at', [$dateFrom, $dateTo])->count(),
                'avg_ctr' => $this->getAverageCTR($dateFrom, $dateTo),
            ],
            'top_performing' => Ad::withCount(['views', 'clicks'])
                ->having('views_count', '>', 0)
                ->orderByRaw('(clicks_count / views_count) DESC')
                ->limit(10)
                ->get(),
            'daily_stats' => $this->getDailyAnalytics($dateFrom, $dateTo),
            'device_breakdown' => $this->getDeviceBreakdown($dateFrom, $dateTo),
            'placement_performance' => $this->getPlacementPerformance($dateFrom, $dateTo),
        ];

        return response()->json($analytics);
    }

    /**
     * Validate request data for storing/updating ads.
     */
    protected function validateAd(Request $request, $adId = null): array
    {
        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial'])],
            'content' => ['required', 'string'],
            'link' => ['nullable', 'url', 'max:2048'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
            'placement' => ['nullable', 'string', 'max:255'],
            'targeting' => ['nullable', 'json'],
            'is_random' => ['nullable', 'boolean'],
            'weight' => ['nullable', 'integer', 'min:1', 'max:10'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'max_impressions' => ['nullable', 'integer', 'min:0'],
            'max_clicks' => ['nullable', 'integer', 'min:0'],
        ];

        $messages = [
            'end_at.after_or_equal' => 'The end date must be a date after or equal to the start date.',
            'link.url' => 'The link must be a valid URL.',
            'type.in' => 'The selected ad type is invalid.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Calculate Click-Through Rate (CTR) for an ad.
     */
    // protected function calculateCTR(Ad $ad): float
    // {
    //     $views = $ad->views_count ?? $ad->views()->count();
    //     $clicks = $ad->clicks_count ?? $ad->clicks()->count();

    //     if ($views === 0) {
    //         return 0.0;
    //     }

    //     return round(($clicks / $views) * 100, 2);
    // }

    /**
     * Apply targeting filters to ad query.
     */
    protected function applyTargetingFilters($query, Request $request)
    {
        $userAgent = $request->userAgent();
        $isMobile = $this->isMobileDevice($userAgent);
        $currentHour = now()->hour;
        $currentUrl = $request->header('referer', '');

        $query->where(function ($q) use ($isMobile, $currentHour, $currentUrl) {
            $q->whereNull('targeting')
              ->orWhere(function ($subQ) use ($isMobile, $currentHour, $currentUrl) {
                  $subQ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(targeting, '$.device')) IS NULL")
                       ->orWhereRaw(
                           "JSON_UNQUOTE(JSON_EXTRACT(targeting, '$.device')) = ?",
                           [$isMobile ? 'mobile' : 'desktop']
                       );
                       
                  $subQ->whereRaw("JSON_EXTRACT(targeting, '$.hours') IS NULL")
                       ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(targeting, '$.hours'), ?)", [$currentHour]);
              });
        });
    }

    /**
     * Check if device is mobile.
     */
    protected function isMobileDevice(string $userAgent): bool
    {
        return preg_match('/Mobile|Android|iPhone|iPad/', $userAgent) === 1;
    }

    /**
     * Get ad statistics for dashboard.
     */
    protected function getAdStatistics(): array
    {
        return [
            'total_ads' => Ad::count(),
            'active_ads' => Ad::active()->count(),
            'expired_ads' => Ad::where('end_at', '<', now())->count(),
            'scheduled_ads' => Ad::where('start_at', '>', now())->count(),
            'total_views_today' => AdView::whereDate('viewed_at', today())->count(),
            'total_clicks_today' => AdClick::whereDate('clicked_at', today())->count(),
            'avg_ctr_today' => $this->getAverageCTR(today(), today()),
        ];
    }

    /**
     * Get daily statistics for an ad.
     */
    // protected function getDailyStats(Ad $ad, int $days = 30): array
    // {
    //     $startDate = now()->subDays($days);
        
    //     return DB::table('ad_views')
    //         ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
    //         ->where('ad_id', $ad->id)
    //         ->where('viewed_at', '>=', $startDate)
    //         ->groupBy('date')
    //         ->orderBy('date')
    //         ->get()
    //         ->toArray();
    // }

    /**
     * Get device statistics for an ad.
     */
    // protected function getDeviceStats(Ad $ad): array
    // {
    //     return DB::table('ad_views')
    //         ->selectRaw('
    //             CASE 
    //                 WHEN viewport_width <= 768 THEN "Mobile"
    //                 WHEN viewport_width <= 1024 THEN "Tablet"
    //                 ELSE "Desktop"
    //             END as device_type,
    //             COUNT(*) as count
    //         ')
    //         ->where('ad_id', $ad->id)
    //         ->groupBy('device_type')
    //         ->get()
    //         ->toArray();
    // }

    /**
     * Get top referrers for an ad.
     */
    protected function getTopReferrers(Ad $ad): array
    {
        return DB::table('ad_views')
            ->selectRaw('referrer, COUNT(*) as count')
            ->where('ad_id', $ad->id)
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get average CTR for date range.
     */
    protected function getAverageCTR(string $dateFrom, string $dateTo): float
    {
        $views = AdView::whereBetween('viewed_at', [$dateFrom, $dateTo])->count();
        $clicks = AdClick::whereBetween('clicked_at', [$dateFrom, $dateTo])->count();

        if ($views === 0) {
            return 0.0;
        }

        return round(($clicks / $views) * 100, 2);
    }

    /**
     * Get daily analytics for date range.
     */
    protected function getDailyAnalytics(string $dateFrom, string $dateTo): array
    {
        $views = DB::table('ad_views')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->whereBetween('viewed_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $clicks = DB::table('ad_clicks')
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as clicks')
            ->whereBetween('clicked_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Merge views and clicks data
        $merged = [];
        foreach ($views as $view) {
            $merged[$view->date] = ['date' => $view->date, 'views' => $view->views, 'clicks' => 0];
        }
        
        foreach ($clicks as $click) {
            if (isset($merged[$click->date])) {
                $merged[$click->date]['clicks'] = $click->clicks;
            } else {
                $merged[$click->date] = ['date' => $click->date, 'views' => 0, 'clicks' => $click->clicks];
            }
        }

        return array_values($merged);
    }

    /**
     * Get device breakdown for analytics.
     */
    protected function getDeviceBreakdown(string $dateFrom, string $dateTo): array
    {
        return DB::table('ad_views')
            ->selectRaw('
                CASE 
                    WHEN viewport_width <= 768 THEN "Mobile"
                    WHEN viewport_width <= 1024 THEN "Tablet"
                    ELSE "Desktop"
                END as device_type,
                COUNT(*) as count
            ')
            ->whereBetween('viewed_at', [$dateFrom, $dateTo])
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

    /**
     * Get placement performance analytics.
     */
    protected function getPlacementPerformance(string $dateFrom, string $dateTo): array
    {
        return DB::table('ads')
            ->join('ad_views', 'ads.id', '=', 'ad_views.ad_id')
            ->leftJoin('ad_clicks', 'ads.id', '=', 'ad_clicks.ad_id')
            ->selectRaw('
                ads.placement,
                COUNT(DISTINCT ad_views.id) as views,
                COUNT(DISTINCT ad_clicks.id) as clicks,
                CASE 
                    WHEN COUNT(DISTINCT ad_views.id) > 0 
                    THEN ROUND((COUNT(DISTINCT ad_clicks.id) / COUNT(DISTINCT ad_views.id)) * 100, 2)
                    ELSE 0 
                END as ctr
            ')
            ->whereBetween('ad_views.viewed_at', [$dateFrom, $dateTo])
            ->groupBy('ads.placement')
            ->orderByDesc('views')
            ->get()
            ->toArray();
    }

    /**
     * Clear ad cache.
     */
    protected function clearAdCache(): void
    {
        Cache::forget('ads.placement.*');
        // You might want to clear all placement caches here
        $placements = ['header', 'sidebar', 'footer', 'in-content', 'floating', 'popup', 'interstitial'];
        foreach ($placements as $placement) {
            Cache::flush(); // Or use more specific cache keys
        }
    }








    /**
 * Display comprehensive analytics for all ads.
 */
public function generalAnalytics(Request $request)
{
    // Get date range from request or default to last 30 days
    $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    
    // Convert to Carbon instances for easier manipulation
    $start = Carbon::parse($startDate)->startOfDay();
    $end = Carbon::parse($endDate)->endOfDay();
    
    // Get all ads with their basic counts
    $ads = Ad::withCount(['views', 'clicks', 'timeSpent'])->get();
    
    // Calculate overall statistics
    $analytics = [
        'overview' => $this->getOverviewStats($start, $end),
        'performance_trends' => $this->getPerformanceTrends($start, $end),
        'time_intervals' => $this->getTimeIntervalStats($start, $end),
        'device_breakdown' => $this->getDeviceBreakdowns($start, $end),
        'geo_distribution' => $this->getGeoDistribution($start, $end),
        'top_performers' => $this->getTopPerformers($start, $end),
        'placement_analysis' => $this->getPlacementAnalysis($start, $end),
        'referrer_analysis' => $this->getReferrerAnalysis($start, $end),
    ];
    
    // Get date range options for the filter
    $dateRanges = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'custom' => 'Custom Range'
    ];
    
    return view('admin.dashboard', compact('analytics', 'startDate', 'endDate', 'dateRanges'));
}

/**
 * Get overview statistics for all ads.
 */
protected function getOverviewStats($startDate, $endDate)
{
    $totalViews = AdView::whereBetween('viewed_at', [$startDate, $endDate])->count();
    $totalClicks = AdClick::whereBetween('clicked_at', [$startDate, $endDate])->count();
    $totalTimeSpent = AdTimeSpent::whereBetween('last_tracked_at', [$startDate, $endDate])->sum('time_spent');
    
    // Calculate CTR
    $ctr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;
    
    // Calculate average time spent per view
    $avgTimeSpent = $totalViews > 0 ? round($totalTimeSpent / $totalViews, 2) : 0;
    
    // Get previous period for comparison
    $daysDiff = $startDate->diffInDays($endDate);
    $prevStartDate = $startDate->copy()->subDays($daysDiff);
    $prevEndDate = $startDate->copy()->subSecond();
    
    $prevViews = AdView::whereBetween('viewed_at', [$prevStartDate, $prevEndDate])->count();
    $prevClicks = AdClick::whereBetween('clicked_at', [$prevStartDate, $prevEndDate])->count();
    
    // Calculate percentage changes
    $viewsChange = $prevViews > 0 ? round((($totalViews - $prevViews) / $prevViews) * 100, 2) : 0;
    $clicksChange = $prevClicks > 0 ? round((($totalClicks - $prevClicks) / $prevClicks) * 100, 2) : 0;
    
    return [
        'total_ads' => Ad::count(),
        'active_ads' => Ad::active()->count(),
        'total_views' => $totalViews,
        'total_clicks' => $totalClicks,
        'total_time_spent' => round($totalTimeSpent, 2),
        'ctr' => $ctr,
        'avg_time_spent' => $avgTimeSpent,
        'views_change' => $viewsChange,
        'clicks_change' => $clicksChange,
    ];
}

/**
 * Get performance trends over time.
 */
protected function getPerformanceTrends($startDate, $endDate)
{
    $days = $startDate->diffInDays($endDate);
    
    // Initialize arrays for each metric
    $viewsData = [];
    $clicksData = [];
    $ctrData = [];
    $timeSpentData = [];
    
    // Generate data for each day
    $currentDate = $startDate->copy();
    while ($currentDate <= $endDate) {
        $nextDate = $currentDate->copy()->addDay();
        
        $dayViews = AdView::whereBetween('viewed_at', [$currentDate, $nextDate])->count();
        $dayClicks = AdClick::whereBetween('clicked_at', [$currentDate, $nextDate])->count();
        $dayTimeSpent = AdTimeSpent::whereBetween('last_tracked_at', [$currentDate, $nextDate])->sum('time_spent');
        $dayCtr = $dayViews > 0 ? round(($dayClicks / $dayViews) * 100, 2) : 0;
        $dayAvgTimeSpent = $dayViews > 0 ? round($dayTimeSpent / $dayViews, 2) : 0;
        
        $dateKey = $currentDate->format('Y-m-d');
        $viewsData[$dateKey] = $dayViews;
        $clicksData[$dateKey] = $dayClicks;
        $ctrData[$dateKey] = $dayCtr;
        $timeSpentData[$dateKey] = $dayAvgTimeSpent;
        
        $currentDate->addDay();
    }
    
    return [
        'views' => $viewsData,
        'clicks' => $clicksData,
        'ctr' => $ctrData,
        'avg_time_spent' => $timeSpentData,
    ];
}

/**
 * Get statistics by time intervals (hour of day, day of week).
 */
protected function getTimeIntervalStats($startDate, $endDate)
{
    // Hourly statistics
    $hourlyStats = AdView::selectRaw('HOUR(viewed_at) as hour, COUNT(*) as views')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();
    
    // Initialize array for all hours
    $hourlyData = array_fill(0, 24, 0);
    foreach ($hourlyStats as $stat) {
        $hourlyData[$stat->hour] = $stat->views;
    }
    
    // Weekday statistics
    $weekdayStats = AdView::selectRaw('DAYOFWEEK(viewed_at) as weekday, COUNT(*) as views')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('weekday')
        ->orderBy('weekday')
        ->get();
    
    // Initialize array for all weekdays
    $weekdayData = array_fill(1, 7, 0);
    foreach ($weekdayStats as $stat) {
        $weekdayData[$stat->weekday] = $stat->views;
    }
    
    return [
        'hourly' => $hourlyData,
        'weekdays' => $weekdayData,
    ];
}

/**
 * Get device breakdown statistics.
 */
protected function getDeviceBreakdowns($startDate, $endDate)
{
    $deviceStats = AdView::selectRaw("
            CASE
                WHEN viewport_width <= 768 THEN 'Mobile'
                WHEN viewport_width <= 1024 THEN 'Tablet'
                ELSE 'Desktop'
            END as device_type,
            COUNT(*) as views
        ")
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('device_type')
        ->get();
    
    $totalViews = $deviceStats->sum('views');
    
    // Calculate clicks for each device type
    $deviceClicks = [];
    foreach ($deviceStats as $device) {
        $sessionIds = AdView::whereBetween('viewed_at', [$startDate, $endDate])
            ->whereRaw("
                CASE
                    WHEN viewport_width <= 768 THEN 'Mobile'
                    WHEN viewport_width <= 1024 THEN 'Tablet'
                    ELSE 'Desktop'
                END = ?
            ", [$device->device_type])
            ->pluck('session_id')
            ->toArray();
        
        $clicks = AdClick::whereIn('session_id', $sessionIds)
            ->whereBetween('clicked_at', [$startDate, $endDate])
            ->count();
            
        $deviceClicks[$device->device_type] = $clicks;
    }
    
    // Calculate percentages and CTR
    $breakdown = [];
    foreach ($deviceStats as $device) {
        $views = $device->views;
        $clicks = $deviceClicks[$device->device_type] ?? 0;
        $percentage = $totalViews > 0 ? round(($views / $totalViews) * 100, 2) : 0;
        $ctr = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
        
        $breakdown[$device->device_type] = [
            'views' => $views,
            'clicks' => $clicks,
            'percentage' => $percentage,
            'ctr' => $ctr,
        ];
    }
    
    return $breakdown;
}

/**
 * Get geographical distribution.
 */
protected function getGeoDistribution($startDate, $endDate)
{
    $countryStats = AdView::selectRaw('country, COUNT(*) as views')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->whereNotNull('country')
        ->groupBy('country')
        ->orderByDesc('views')
        ->limit(10)
        ->get();
    
    $cityStats = AdView::selectRaw('city, country, COUNT(*) as views')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->whereNotNull('city')
        ->groupBy('city', 'country')
        ->orderByDesc('views')
        ->limit(10)
        ->get();
    
    return [
        'countries' => $countryStats,
        'cities' => $cityStats,
    ];
}

/**
 * Get top performing ads.
 */
protected function getTopPerformers($startDate, $endDate)
{
    $topByViews = Ad::withCount(['views', 'clicks'])
        ->whereHas('views', function($query) use ($startDate, $endDate) {
            $query->whereBetween('viewed_at', [$startDate, $endDate]);
        })
        ->orderByDesc('views_count')
        ->limit(5)
        ->get();
    
    $topByClicks = Ad::withCount(['views', 'clicks'])
        ->whereHas('clicks', function($query) use ($startDate, $endDate) {
            $query->whereBetween('clicked_at', [$startDate, $endDate]);
        })
        ->orderByDesc('clicks_count')
        ->limit(5)
        ->get();
    
    $topByCTR = Ad::withCount(['views', 'clicks'])
        ->whereHas('views', function($query) use ($startDate, $endDate) {
            $query->whereBetween('viewed_at', [$startDate, $endDate]);
        })
        ->having('views_count', '>', 10) // Minimum views threshold
        ->get()
        ->map(function($ad) {
            $ad->ctr = $ad->views_count > 0 ? round(($ad->clicks_count / $ad->views_count) * 100, 2) : 0;
            return $ad;
        })
        ->sortByDesc('ctr')
        ->take(5);
    
    return [
        'by_views' => $topByViews,
        'by_clicks' => $topByClicks,
        'by_ctr' => $topByCTR,
    ];
}

/**
 * Get placement analysis.
 */
protected function getPlacementAnalysis($startDate, $endDate)
{
    $placementStats = DB::table('ads')
        ->join('ad_views', 'ads.id', '=', 'ad_views.ad_id')
        ->leftJoin('ad_clicks', function($join) use ($startDate, $endDate) {
            $join->on('ads.id', '=', 'ad_clicks.ad_id')
                 ->whereBetween('ad_clicks.clicked_at', [$startDate, $endDate]);
        })
        ->selectRaw('
            ads.placement,
            COUNT(DISTINCT ad_views.id) as views,
            COUNT(DISTINCT ad_clicks.id) as clicks,
            CASE 
                WHEN COUNT(DISTINCT ad_views.id) > 0 
                THEN ROUND((COUNT(DISTINCT ad_clicks.id) / COUNT(DISTINCT ad_views.id)) * 100, 2)
                ELSE 0 
            END as ctr
        ')
        ->whereBetween('ad_views.viewed_at', [$startDate, $endDate])
        ->groupBy('ads.placement')
        ->orderByDesc('views')
        ->get();
    
    return $placementStats;
}

/**
 * Get referrer analysis.
 */
protected function getReferrerAnalysis($startDate, $endDate)
{
    $referrerStats = AdView::selectRaw('
            COALESCE(referrer, \'Direct\') as referrer,
            COUNT(*) as views,
            COUNT(DISTINCT session_id) as unique_sessions
        ')
        ->whereBetween('viewed_at', [$startDate, $endDate])
        ->groupBy('referrer')
        ->orderByDesc('views')
        ->limit(10)
        ->get();
    
    // Get clicks for each referrer
    foreach ($referrerStats as $stat) {
        $clicks = AdClick::whereBetween('clicked_at', [$startDate, $endDate])
            ->where('referrer', $stat->referrer === 'Direct' ? null : $stat->referrer)
            ->count();
            
        $stat->clicks = $clicks;
        $stat->ctr = $stat->views > 0 ? round(($clicks / $stat->views) * 100, 2) : 0;
    }
    
    return $referrerStats;
}





}


























