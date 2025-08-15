<?php
namespace App\Services;

use App\Models\Ad;
use App\Models\AdView;
use App\Models\AdClick;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdService
{
    /**
     * Get ads for a specific placement with advanced targeting.
     */
    public function getAdsForPlacement(string $placement, Request $request, int $limit = 3): Collection
    {
        $cacheKey = $this->generateCacheKey($placement, $request);
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($placement, $request, $limit) {
            $query = Ad::active()
                ->currentlyRunning()
                ->where('placement', $placement) 
                ->where(function ($q) {
                    $q->whereRaw('(max_impressions IS NULL OR (SELECT COUNT(*) FROM ad_views WHERE ad_id = ads.id) < max_impressions)')
                      ->whereRaw('(max_clicks IS NULL OR (SELECT COUNT(*) FROM ad_clicks WHERE ad_id = ads.id) < max_clicks)');
                });

            // Apply advanced targeting
            $this->applyTargeting($query, $request);

            $ads = $query->get();

            // Handle weighted random selection
            if ($ads->where('is_random', true)->isNotEmpty()) {
                $ads = $this->selectRandomWeightedAds($ads, $limit);
            }

            return $ads->take($limit);
        });
    }

    /**
     * Apply targeting rules to query.
     */
    protected function applyTargeting($query, Request $request): void
    {
        $userAgent = $request->userAgent();
        $deviceType = $this->getDeviceType($userAgent);
        $currentHour = now()->hour;
        $currentUrl = $request->url();
        $referrer = $request->header('referer');

        $query->where(function ($q) use ($deviceType, $currentHour, $currentUrl, $referrer) {
            $q->whereNull('targeting')
              ->orWhere(function ($subQ) use ($deviceType, $currentHour, $currentUrl, $referrer) {
                  // Device targeting
                  $subQ->where(function ($deviceQ) use ($deviceType) {
                      $deviceQ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(targeting, '$.device')) IS NULL")
                              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(targeting, '$.device')) = ?", [$deviceType]);
                  });

                  // Time targeting
                  $subQ->where(function ($timeQ) use ($currentHour) {
                      $timeQ->whereRaw("JSON_EXTRACT(targeting, '$.hours') IS NULL")
                            ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(targeting, '$.hours'), ?)", [$currentHour]);
                  });

                  // URL targeting
                  $subQ->where(function ($urlQ) use ($currentUrl) {
                      $urlQ->whereRaw("JSON_EXTRACT(targeting, '$.urls') IS NULL")
                           ->orWhereRaw("JSON_SEARCH(JSON_EXTRACT(targeting, '$.urls'), 'one', ?) IS NOT NULL", ['%' . parse_url($currentUrl, PHP_URL_PATH) . '%']);
                  });

                  // Referrer targeting
                  if ($referrer) {
                      $subQ->where(function ($refQ) use ($referrer) {
                          $refQ->whereRaw("JSON_EXTRACT(targeting, '$.referrers') IS NULL")
                               ->orWhereRaw("JSON_SEARCH(JSON_EXTRACT(targeting, '$.referrers'), 'one', ?) IS NOT NULL", ['%' . parse_url($referrer, PHP_URL_HOST) . '%']);
                      });
                  }
              });
        });
    }

    /**
     * Select random weighted ads.
     */
    protected function selectRandomWeightedAds(Collection $ads, int $limit): Collection
    {
        $randomAds = $ads->where('is_random', true);
        $staticAds = $ads->where('is_random', false);

        if ($randomAds->isEmpty()) {
            return $staticAds->take($limit);
        }

        // Create weighted selection
        $weightedAds = [];
        foreach ($randomAds as $ad) {
            $weight = $ad->weight ?? 1;
            for ($i = 0; $i < $weight; $i++) {
                $weightedAds[] = $ad;
            }
        }

        shuffle($weightedAds);
        $selectedRandom = collect(array_slice($weightedAds, 0, min($limit, count($weightedAds))));

        // Remove duplicates
        $selectedRandom = $selectedRandom->unique('id');

        // Fill remaining slots with static ads
        $remaining = $limit - $selectedRandom->count();
        if ($remaining > 0) {
            $selectedRandom = $selectedRandom->merge($staticAds->take($remaining));
        }

        return $selectedRandom->take($limit);
    }

    /**
     * Get device type from user agent.
     */
    protected function getDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Generate cache key for ad placement.
     */
    protected function generateCacheKey(string $placement, Request $request): string
    {
        $factors = [
            $placement,
            $this->getDeviceType($request->userAgent()),
            now()->hour,
            md5($request->url()),
        ];

        return 'ads.placement.' . md5(implode('|', $factors));
    }

    /**
     * Record ad impression with fraud detection.
     */
    public function recordImpression(int $adId, Request $request): bool
    {
        try {
            // Basic fraud detection
            if ($this->isPotentialFraud($request)) {
                Log::warning('Potential ad fraud detected', [
                    'ad_id' => $adId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return false;
            }

            AdView::create([
                'ad_id' => $adId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'url' => $request->input('url', $request->url()),
                'viewport_width' => $request->input('viewport.width'),
                'viewport_height' => $request->input('viewport.height'),
                'viewed_at' => now(),
                'session_id' => $request->session()->getId(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record ad impression', [
                'ad_id' => $adId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Record ad click with fraud detection.
     */
    public function recordClick(int $adId, Request $request): bool
    {
        try {
            // Basic fraud detection
            if ($this->isPotentialFraud($request)) {
                Log::warning('Potential ad click fraud detected', [
                    'ad_id' => $adId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return false;
            }

            AdClick::create([
                'ad_id' => $adId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->input('url', $request->url()),
                'target_url' => $request->input('target_url'),
                'clicked_at' => now(),
                'session_id' => $request->session()->getId(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record ad click', [
                'ad_id' => $adId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Basic fraud detection.
     */
    protected function isPotentialFraud(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Check for bot user agents
        if (preg_match('/bot|crawl|spider|scrape/i', $userAgent)) {
            return true;
        }

        // Check for excessive requests from same IP
        $recentViews = AdView::where('ip_address', $ip)
            ->where('viewed_at', '>', now()->subMinutes(1))
            ->count();

        if ($recentViews > 10) {
            return true;
        }

        return false;
    }

    /**
     * Clear ad cache.
     */
    public function clearCache(): void
    {
        Cache::tags(['ads'])->flush();
    }
}