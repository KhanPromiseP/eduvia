<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Ad;
use App\Models\AdView;
use App\Models\AdClick;
use App\Models\AdTimeSpent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdninDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date range handling
        $dateRanges = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_7_days' => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
        ];

        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Apply quick date ranges
        if ($request->has('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $startDate = Carbon::now()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday()->format('Y-m-d');
                    $endDate = Carbon::yesterday()->format('Y-m-d');
                    break;
                case 'last_7_days':
                    $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'last_30_days':
                    $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->format('Y-m-d');
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                    break;
            }
        }

        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get analytics data
        $analytics = $this->getAnalyticsData($start, $end);

        return view('admin.dashboard', compact(
            'analytics',
            'dateRanges',
            'startDate',
            'endDate'
        ));
    }

    private function getAnalyticsData($startDate, $endDate)
    {
        return [
            'overview' => $this->getOverviewStats($startDate, $endDate),
            'performance_trends' => $this->getPerformanceTrends($startDate, $endDate),
            'device_breakdown' => $this->getDeviceBreakdowns($startDate, $endDate),
            'top_performers' => $this->getTopPerformers($startDate, $endDate),
            'placement_analysis' => $this->getPlacementAnalysis($startDate, $endDate),
        ];
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
        $maxDays = 30; // Limit to prevent too much data
        
        if ($days > $maxDays) {
            // Group by week if too many days
            $groupBy = 'week';
            $currentDate = $startDate->copy()->startOfWeek();
        } else {
            // Group by day
            $groupBy = 'day';
            $currentDate = $startDate->copy();
        }
        
        // Initialize arrays for each metric
        $viewsData = [];
        $clicksData = [];
        
        while ($currentDate <= $endDate) {
            if ($groupBy === 'week') {
                $nextDate = $currentDate->copy()->endOfWeek();
                if ($nextDate > $endDate) {
                    $nextDate = $endDate;
                }
                $dateKey = $currentDate->format('M j') . ' - ' . $nextDate->format('M j');
            } else {
                $nextDate = $currentDate->copy()->addDay();
                $dateKey = $currentDate->format('Y-m-d');
            }
            
            $dayViews = AdView::whereBetween('viewed_at', [$currentDate, $nextDate])->count();
            $dayClicks = AdClick::whereBetween('clicked_at', [$currentDate, $nextDate])->count();
            
            $viewsData[$dateKey] = $dayViews;
            $clicksData[$dateKey] = $dayClicks;
            
            if ($groupBy === 'week') {
                $currentDate->addWeek();
            } else {
                $currentDate->addDay();
            }
        }
        
        return [
            'views' => $viewsData,
            'clicks' => $clicksData,
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
            ->get()
            ->map(function($ad) {
                $ad->ctr = $ad->views_count > 0 ? round(($ad->clicks_count / $ad->views_count) * 100, 2) : 0;
                return $ad;
            });
        
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
}