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

class AdController extends Controller
{
public function index(Request $request)
{
    // Start with the admin scope that shows all ads
    $ads = Ad::forAdmin();

    // Apply filters only if they're explicitly requested
    if ($request->has('status') && $status = $request->status) {
        $ads->where('is_active', $status === 'active');
    }

    if ($request->has('search') && $search = $request->search) {
        $ads->where('title', 'like', "%{$search}%");
    }
    // Filters
    if ($status = $request->status) {
        $ads->where('is_active', $status === 'active');
    }

    if ($search = $request->search) {
        $ads->where('title', 'like', "%{$search}%");
    }

    if ($weight = $request->weight) {
        $ads->where('weight', $weight);
    }

    if ($request->has('is_random') && $request->is_random) {
        $ads->where('is_random', true);
    }

    if ($startDate = $request->start_date) {
        $ads->where('start_at', '>=', $startDate);
    }

    if ($endDate = $request->end_date) {
        $ads->where('end_at', '<=', $endDate);
    }

    if ($type = $request->type) {
        if ($type !== 'all') {
            $ads->where('type', $type);
        }
    }

    if ($placement = $request->placement) {
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
        $ad->loadCount(['views', 'clicks']);

        $analytics = [
            'total_views' => $ad->views_count ?? 0,
            'total_clicks' => $ad->clicks_count ?? 0,
            'ctr' => $this->calculateCTR($ad),
            'total_time_spent' => $this->getTotalTimeSpent($ad),
            'daily_stats' => $this->getDailyStats($ad),
            'device_stats' => $this->getDeviceStats($ad),
            'top_referrers' => $this->getTopReferrers($ad),
        ];

        return view('admin.ads.show', compact('ad', 'analytics'));
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

    /**
     * Track ad view.
     */
    public function trackView(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ad_id' => 'required|exists:ads,id',
                'timestamp' => 'required|integer',
                'url' => 'required|url',
                'viewport' => 'array',
            ]);

            AdView::create([
                'ad_id' => $request->ad_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->referrer,
                'url' => $request->url,
                'viewport_width' => $request->input('viewport.width'),
                'viewport_height' => $request->input('viewport.height'),
                'viewed_at' => Carbon::createFromTimestamp($request->timestamp / 1000),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Ad view tracking failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Track ad click.
     */
    public function trackClick(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ad_id' => 'required|exists:ads,id',
                'timestamp' => 'required|integer',
                'url' => 'required|url',
                'target_url' => 'nullable|url',
            ]);

            AdClick::create([
                'ad_id' => $request->ad_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->url,
                'target_url' => $request->target_url,
                'clicked_at' => Carbon::createFromTimestamp($request->timestamp / 1000),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Ad click tracking failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['success' => false], 500);
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
    protected function calculateCTR(Ad $ad): float
    {
        $views = $ad->views_count ?? $ad->views()->count();
        $clicks = $ad->clicks_count ?? $ad->clicks()->count();

        if ($views === 0) {
            return 0.0;
        }

        return round(($clicks / $views) * 100, 2);
    }

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
    protected function getDailyStats(Ad $ad, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return DB::table('ad_views')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->where('ad_id', $ad->id)
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get device statistics for an ad.
     */
    protected function getDeviceStats(Ad $ad): array
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
            ->where('ad_id', $ad->id)
            ->groupBy('device_type')
            ->get()
            ->toArray();
    }

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
}