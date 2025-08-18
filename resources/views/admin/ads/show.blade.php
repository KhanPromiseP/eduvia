@extends('layouts.admin')

@section('header')
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 -m-6 mb-8 px-6 py-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('admin.ads.index') }}" 
                   class="text-white hover:text-blue-100 transition-colors p-2 rounded-lg hover:bg-white/10">
                    <i class="bi bi-arrow-left text-2xl"></i>
                </a>
                <div class="text-white">
                    <h1 class="text-3xl font-bold">
                        {{ $ad->title ?? 'Untitled Ad' }}
                    </h1>
                    <p class="text-blue-100 mt-2 flex items-center space-x-4">
                        <span class="capitalize bg-white/20 px-3 py-1 rounded-full text-sm">
                            {{ $ad->type }} Ad
                        </span>
                        <span class="flex items-center">
                            <i class="bi bi-calendar mr-2"></i>
                            Created {{ $ad->created_at->diffForHumans() }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Status Badge -->
                @if($ad->is_active && (!$ad->end_at || $ad->end_at->isFuture()))
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-500 text-white shadow-lg animate-pulse">
                        <i class="bi bi-check-circle-fill mr-2"></i> Active
                    </span>
                @elseif($ad->end_at && $ad->end_at->isPast())
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-500 text-white shadow-lg">
                        <i class="bi bi-clock-fill mr-2"></i> Expired
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-500 text-white shadow-lg">
                        <i class="bi bi-pause-circle-fill mr-2"></i> Inactive
                    </span>
                @endif
                
                <!-- Action Buttons -->
                <a href="{{ route('admin.ads.edit', $ad) }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 font-semibold">
                    <i class="bi bi-pencil mr-2"></i> Edit Ad
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
        border: 0;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .chart-container {
        position: relative;
        height: 350px;
        margin: 24px 0;
    }
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 1px solid #e5e7eb;
        border-radius: 16px 16px 0 0;
    }
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        background-color: #e5e7eb;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        background: linear-gradient(90deg, #3b82f6, #6366f1);
    }
    .hourly-activity {
        display: grid;
        grid-template-columns: repeat(24, 1fr);
        gap: 2px;
        height: 60px;
    }
    .hour-cell {
        background-color: #e5e7eb;
        border-radius: 2px;
        position: relative;
    }
    .hour-cell.active {
        background-color: #3b82f6;
    }
    .hour-cell:hover .hour-tooltip {
        display: block;
    }
    .hour-tooltip {
        display: none;
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
        margin-bottom: 5px;
    }
    .hour-tooltip:after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-3 py-2 max-w-7xl">
    <!-- Performance Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 mb-12">
        <!-- Total Views -->
        <div class="card p-8 border-l-4 border-gray-500 bg-gradient-to-br from-gray-200 to-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-900 text-sm font-medium uppercase tracking-wide">Total Views</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($analytics['total_views']) }}</p>
                    <div class="mt-2 flex items-center">
                        @php
                            $viewsChange = $analytics['views_change'] ?? 0;
                            $viewsTrend = $viewsChange >= 0 ? 'up' : 'down';
                        @endphp
                        <span class="text-xs font-medium {{ $viewsTrend === 'up' ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi bi-arrow-{{ $viewsTrend }}-right mr-1"></i>
                            {{ abs($viewsChange) }}% {{ $viewsTrend === 'up' ? 'increase' : 'decrease' }}
                        </span>
                        <span class="text-gray-400 text-xs ml-2">vs last period</span>
                    </div>
                </div>
                <div class="p-4 bg-gray-300 rounded-2xl backdrop-blur-sm">
                    <i class="bi bi-eye text-3xl text-gray"></i>
                </div>
            </div>
        </div>

        <!-- Total Clicks -->
        <div class="card p-8 border-l-4 border-green-500 bg-gradient-to-br from-green-50 to-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium uppercase tracking-wide">Total Clicks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($analytics['total_clicks']) }}</p>
                    <div class="mt-2 flex items-center">
                        @php
                            $clicksChange = $analytics['clicks_change'] ?? 0;
                            $clicksTrend = $clicksChange >= 0 ? 'up' : 'down';
                        @endphp
                        <span class="text-xs font-medium {{ $clicksTrend === 'up' ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi bi-arrow-{{ $clicksTrend }}-right mr-1"></i>
                            {{ abs($clicksChange) }}% {{ $clicksTrend === 'up' ? 'increase' : 'decrease' }}
                        </span>
                        <span class="text-gray-400 text-xs ml-2">vs last period</span>
                    </div>
                </div>
                <div class="p-4 bg-green-100 rounded-2xl">
                    <i class="bi bi-cursor text-3xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Click-Through Rate -->
        <div class="card p-8 border-l-4 border-purple-500 bg-gradient-to-br from-purple-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-600 text-sm font-medium uppercase tracking-wide">Click Rate</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $analytics['ctr'] }}%</p>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Industry Avg: 2.5%</span>
                            <span>{{ round($analytics['ctr'] / 2.5 * 100) }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(100, $analytics['ctr'] / 2.5 * 100) }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-purple-100 rounded-2xl">
                    <i class="bi bi-graph-up text-3xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Time Spent -->
        <div class="card p-8 border-l-4 border-yellow-500 bg-gradient-to-br from-yellow-50 to-orange-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-600 text-sm font-medium uppercase tracking-wide">Engagement</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ gmdate('H:i:s', $analytics['total_time_spent']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Avg: {{ $analytics['avg_time_spent'] ?? 0 }}s per view
                    </p>
                </div>
                <div class="p-4 bg-yellow-100 rounded-2xl">
                    <i class="bi bi-stopwatch text-3xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Ad Details Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-8 py-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-4">
                            <i class="bi bi-info-circle text-blue-600 text-lg"></i>
                        </div>
                        Ad Information
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Title</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">{{ $ad->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Type</dt>
                                <dd class="mt-2">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 capitalize">
                                        {{ $ad->type }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Creator</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">{{ $ad->user->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Product</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">{{ $ad->product->title ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Placement</dt>
                                <dd class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 capitalize">
                                        {{ $ad->placement ?? 'Any' }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Weight</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">{{ $ad->weight ?? 'Default' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Start Date</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">
                                    {{ $ad->start_at ? $ad->start_at->format('M d, Y H:i') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">End Date</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">
                                    {{ $ad->end_at ? $ad->end_at->format('M d, Y H:i') : 'No end date' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Max Impressions</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">
                                    @if($ad->max_impressions)
                                        {{ number_format($ad->max_impressions) }}
                                        @if($analytics['total_views'] > 0)
                                            <div class="progress-bar mt-2">
                                                <div class="progress-fill" style="width: {{ min(100, ($analytics['total_views'] / $ad->max_impressions) * 100) }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ number_format(($analytics['total_views'] / $ad->max_impressions) * 100, 1) }}% used
                                            </p>
                                        @endif
                                    @else
                                        Unlimited
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Max Clicks</dt>
                                <dd class="mt-2 text-lg font-medium text-gray-900">
                                    @if($ad->max_clicks)
                                        {{ number_format($ad->max_clicks) }}
                                        @if($analytics['total_clicks'] > 0)
                                            <div class="progress-bar mt-2">
                                                <div class="progress-fill" style="width: {{ min(100, ($analytics['total_clicks'] / $ad->max_clicks) * 100) }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ number_format(($analytics['total_clicks'] / $ad->max_clicks) * 100, 1) }}% used
                                            </p>
                                        @endif
                                    @else
                                        Unlimited
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>
                    
                    @if($ad->link)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Target Link</dt>
                            <dd class="mt-3">
                                <a href="{{ $ad->link }}" target="_blank" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium break-all">
                                    <i class="bi bi-link-45deg mr-2"></i>
                                    {{ $ad->link }}
                                    <i class="bi bi-box-arrow-up-right ml-2"></i>
                                </a>
                            </dd>
                        </div>
                    @endif

                    @if($ad->targeting)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Targeting Rules</dt>
                            <dd>
                                <div class="bg-gray-50 rounded-xl p-4 border">
                                    <pre class="text-sm text-gray-800 overflow-x-auto">{{ json_encode($ad->targeting, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ad Content Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-8 py-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-4">
                            <i class="bi bi-code-square text-green-600 text-lg"></i>
                        </div>
                        Ad Content Preview
                    </h3>
                </div>
                <div class="p-8">
                    @if($ad->type === 'image' && filter_var($ad->content, FILTER_VALIDATE_URL))
                        <div class="text-center bg-gray-50 rounded-xl p-8">
                            <img src="{{ $ad->content }}" alt="{{ $ad->title }}" 
                                 class="max-w-full h-auto rounded-xl shadow-lg mx-auto border"
                                 style="max-height: 400px;">
                        </div>
                    @elseif($ad->type === 'video' && filter_var($ad->content, FILTER_VALIDATE_URL))
                        <div class="text-center bg-gray-50 rounded-xl p-8">
                            <video controls class="max-w-full h-auto rounded-xl shadow-lg mx-auto border" style="max-height: 400px;">
                                <source src="{{ $ad->content }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-xl p-6 border">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-x-auto">{{ $ad->content }}</pre>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Charts -->
            <div class="card overflow-hidden">
                <div class="card-header px-8 py-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg mr-4">
                            <i class="bi bi-graph-up text-purple-600 text-lg"></i>
                        </div>
                        Performance Analytics (Last 30 Days)
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Hourly Activity</h4>
                            <div class="hourly-activity">
                                @foreach(range(0, 23) as $hour)
                                    @php
                                        $hourViews = $analytics['hourly_stats'][$hour] ?? 0;
                                        $maxHourViews = max(array_values($analytics['hourly_stats'] ?? [1]));
                                        $height = $maxHourViews > 0 ? ($hourViews / $maxHourViews * 100) : 0;
                                    @endphp
                                    <div class="hour-cell {{ $hourViews > 0 ? 'active' : '' }}" style="height: {{ $height }}%">
                                        <div class="hour-tooltip">
                                            {{ $hour }}:00 - {{ $hourViews }} views
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>12 AM</span>
                                <span>6 AM</span>
                                <span>12 PM</span>
                                <span>6 PM</span>
                                <span>12 AM</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Engagement Rate</h4>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-bold">{{ $analytics['engagement_rate'] ?? 0 }}%</span>
                                @php
                                    $engagementChange = $analytics['engagement_change'] ?? 0;
                                    $engagementTrend = $engagementChange >= 0 ? 'up' : 'down';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $engagementTrend === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="bi bi-arrow-{{ $engagementTrend }}-right mr-1"></i>
                                    {{ abs($engagementChange) }}%
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">Percentage of users who interacted with the ad beyond viewing</p>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Views by Day of Week</h4>
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="weekdayChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Click Distribution</h4>
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="clickDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Settings Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-gray-100 rounded-lg mr-3">
                            <i class="bi bi-gear text-gray-600"></i>
                        </div>
                        Configuration
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Random Display</span>
                        @if($ad->is_random)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="bi bi-shuffle mr-1"></i> Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                <i class="bi bi-arrow-right mr-1"></i> Disabled
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Budget</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($ad->budget)
                                ${{ number_format($ad->budget, 2) }}
                                @if($analytics['cost_per_click'])
                                    <span class="text-xs text-gray-500 block text-right">
                                        ${{ number_format($analytics['cost_per_click'], 4) }} per click
                                    </span>
                                @endif
                            @else
                                No limit
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Created</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ad->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Last Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ad->updated_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Performance Summary</h4>
                        <div class="space-y-3">
                            {{-- <div class="flex justify-between">
                                <span class="text-sm text-gray-500">First Impression</span>
                                <span class="text-sm font-medium">
                                    {{ $analytics['first_impression'] ? $analytics['first_impression']->format('M d, Y') : 'Never' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Last Impression</span>
                                <span class="text-sm font-medium">
                                    {{ $analytics['last_impression'] ? $analytics['last_impression']->format('M d, Y') : 'Never' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">First Click</span>
                                <span class="text-sm font-medium">
                                    {{ $analytics['first_click'] ? $analytics['first_click']->format('M d, Y') : 'Never' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Last Click</span>
                                <span class="text-sm font-medium">
                                    {{ $analytics['last_click'] ? $analytics['last_click']->format('M d, Y') : 'Never' }}
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Stats Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                            <i class="bi bi-device-hdd text-indigo-600"></i>
                        </div>
                        Device Analytics
                    </h3>
                </div>
                <div class="p-6">
                    @if(count($analytics['device_stats']) > 0)
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="deviceChart"></canvas>
                        </div>
                        <div class="mt-4 space-y-3">
                            @foreach($analytics['device_stats'] as $device)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ $device->device_type }}</span>
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold mr-2">{{ $device->count }}</span>
                                        <span class="text-xs text-gray-500">
                                            ({{ round(($device->count / $analytics['total_views']) * 100, 1) }}%)
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="bi bi-bar-chart text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-sm">No device data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Geographic Stats Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <i class="bi bi-globe text-green-600"></i>
                        </div>
                        Geographic Distribution
                    </h3>
                </div>
                <div class="p-6">
                    {{-- @if(count($analytics['geo_stats']) > 0)
                        <div class="space-y-4">
                            @foreach($analytics['geo_stats'] as $geo)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-700 mr-2">
                                            <i class="bi bi-geo-alt-fill text-gray-400"></i>
                                            {{ $geo->country ?? 'Unknown' }}
                                        </span>
                                        @if($geo->region)
                                            <span class="text-xs text-gray-500">({{ $geo->region }})</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold">{{ $geo->count }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View Full Geographic Report
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="bi bi-globe text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-sm">No geographic data available</p>
                        </div>
                    @endif --}}
                </div>
            </div>

            <!-- Top Referrers Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg mr-3">
                            <i class="bi bi-link-45deg text-orange-600"></i>
                        </div>
                        Top Referrers
                    </h3>
                </div>
                <div class="p-6">
                    @if(count($analytics['top_referrers']) > 0)
                        <div class="space-y-4">
                            @foreach($analytics['top_referrers'] as $referrer)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" title="{{ $referrer->referrer }}">
                                            <i class="bi bi-globe text-gray-400 mr-2"></i>
                                            {{ parse_url($referrer->referrer, PHP_URL_HOST) ?? $referrer->referrer }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $referrer->count }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="bi bi-link text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-sm">No referrer data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card overflow-hidden">
                <div class="card-header px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <i class="bi bi-lightning text-yellow-600"></i>
                        </div>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('admin.ads.edit', $ad) }}" 
                       class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 hover:shadow-md">
                        <i class="bi bi-pencil mr-2"></i>
                        Edit Advertisement
                    </a>
                    
                    @if($ad->is_active)
                        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="0">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-red-300 shadow-sm text-sm font-semibold rounded-xl text-red-700 bg-red-50 hover:bg-red-100 transition-all duration-200 hover:shadow-md">
                                <i class="bi bi-pause mr-2"></i>
                                Pause Advertisement
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="1">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-green-300 shadow-sm text-sm font-semibold rounded-xl text-green-700 bg-green-50 hover:bg-green-100 transition-all duration-200 hover:shadow-md">
                                <i class="bi bi-play mr-2"></i>
                                Activate Advertisement
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" 
                          class="w-full" 
                          onsubmit="return confirm('Are you sure you want to delete this advertisement? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 border border-red-600 shadow-sm text-sm font-semibold rounded-xl text-white bg-red-600 hover:bg-red-700 transition-all duration-200 hover:shadow-md">
                            <i class="bi bi-trash mr-2"></i>
                            Delete Advertisement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get data from Laravel
        const dailyStats = @json($analytics['daily_stats'] ?? []);
        const deviceStats = @json($analytics['device_stats'] ?? []);
        const weekdayStats = @json($analytics['weekday_stats'] ?? []);
        const clickDistribution = @json($analytics['click_distribution'] ?? []);

        // Performance Chart
        if (dailyStats && dailyStats.length > 0) {
            const performanceCtx = document.getElementById('performanceChart').getContext('2d');
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: dailyStats.map(stat => stat.date),
                    datasets: [
                        {
                            label: 'Views',
                            data: dailyStats.map(stat => stat.views),
                            borderColor: 'rgb(99, 102, 241)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: 'rgb(99, 102, 241)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        },
                        {
                            label: 'Clicks',
                            data: dailyStats.map(stat => stat.clicks),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: 'rgb(16, 185, 129)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Show message if no data
            document.getElementById('performanceChart').parentNode.innerHTML = 
                '<div class="text-center py-16"><i class="bi bi-graph-up text-6xl text-gray-300 mb-4"></i><p class="text-gray-500 text-sm">No performance data available yet</p></div>';
        }

        // Device Chart
        if (deviceStats && deviceStats.length > 0) {
            const deviceCtx = document.getElementById('deviceChart').getContext('2d');
            new Chart(deviceCtx, {
                type: 'doughnut',
                data: {
                    labels: deviceStats.map(stat => stat.device_type),
                    datasets: [{
                        data: deviceStats.map(stat => stat.count),
                        backgroundColor: [
                            '#6366f1',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#06b6d4',
                            '#84cc16'
                        ],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverBorderWidth: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 24,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                },
                                color: '#374151'
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Weekday Chart
        if (weekdayStats && weekdayStats.length > 0) {
            const weekdayCtx = document.getElementById('weekdayChart').getContext('2d');
            new Chart(weekdayCtx, {
                type: 'bar',
                data: {
                    labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [{
                        label: 'Views',
                        data: weekdayStats,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        // Click Distribution Chart
        if (clickDistribution && clickDistribution.length > 0) {
            const clickDistCtx = document.getElementById('clickDistributionChart').getContext('2d');
            new Chart(clickDistCtx, {
                type: 'radar',
                data: {
                    labels: ['First Hour', 'First Day', 'First Week', 'First Month', 'Beyond Month'],
                    datasets: [{
                        label: 'Click Timing',
                        data: clickDistribution,
                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                        borderColor: 'rgb(245, 158, 11)',
                        pointBackgroundColor: 'rgb(245, 158, 11)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(245, 158, 11)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        r: {
                            angleLines: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            suggestedMin: 0,
                            ticks: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush