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
    <!-- Performance Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 mb-12">

    <!-- Total Views -->
    <div class="card p-8 border-l-4 border-gray-500 bg-gradient-to-br from-gray-200 to-emerald-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-900 text-sm font-medium uppercase tracking-wide">Total Views</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($ad->views_count) }}</p>

                <!-- Trends -->
                <div class="mt-3 space-y-1 text-xs">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $periodKey => $label)
                        @php
                            $change = $analytics[$periodKey]['views_change'] ?? 0;
                            $trend = $change >= 0 ? 'up' : 'down';
                        @endphp
                        <div class="flex items-center">
                            <span class="w-14 text-gray-500">{{ $label }}</span>
                            <span class="{{ $trend === 'up' ? 'text-green-600' : 'text-red-600' }}">
                                <i class="bi bi-arrow-{{ $trend }}-right mr-1"></i>
                                {{ abs($change) }}% {{ $trend === 'up' ? 'increase' : 'decrease' }}
                            </span>
                        </div>
                    @endforeach
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
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($ad->clicks_count) }}</p>

                <!-- Trends -->
                <div class="mt-3 space-y-1 text-xs">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $periodKey => $label)
                        @php
                            $change = $analytics[$periodKey]['clicks_change'] ?? 0;
                            $trend = $change >= 0 ? 'up' : 'down';
                        @endphp
                        <div class="flex items-center">
                            <span class="w-14 text-gray-500">{{ $label }}</span>
                            <span class="{{ $trend === 'up' ? 'text-green-600' : 'text-red-600' }}">
                                <i class="bi bi-arrow-{{ $trend }}-right mr-1"></i>
                                {{ abs($change) }}% {{ $trend === 'up' ? 'increase' : 'decrease' }}
                            </span>
                        </div>
                    @endforeach
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
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $analytics['daily']['ctr'] }}%</p>

                <!-- Trends -->
                <div class="mt-3 space-y-1 text-xs">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $periodKey => $label)
                        @php
                            $ctr = $analytics[$periodKey]['ctr'] ?? 0;
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-gray-500 w-14">{{ $label }}</span>
                            <span class="text-gray-900 font-medium">{{ $ctr }}%</span>
                        </div>
                    @endforeach
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
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ gmdate('H:i:s', $analytics['daily']['time_spent']['total'] ?? 0) }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    Avg: {{ $analytics['daily']['time_spent']['average'] ?? 0 }}s per view
                </p>

                <!-- Trends -->
                <div class="mt-3 space-y-1 text-xs">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $periodKey => $label)
                        @php
                            $avg = $analytics[$periodKey]['time_spent']['average'] ?? 0;
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-gray-500 w-14">{{ $label }}</span>
                            <span class="text-gray-900 font-medium">{{ $avg }}s</span>
                        </div>
                    @endforeach
                </div>
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

@php
    $hourlyStats = $analytics['hourly_stats'] ?? array_fill(0, 24, 0);
    $maxHourViews = max($hourlyStats) ?: 1;
@endphp

<div class="grid grid-cols-12 gap-1 h-40 mb-5">
    @foreach(range(0, 11) as $hour)
        @php
            $views = $hourlyStats[$hour] ?? 0;
            $height = ($views / $maxHourViews) * 100;
        @endphp
        <div class="flex flex-col items-center justify-end relative group">
            <div class="bg-purple-500 w-3 rounded-t-md transition-all duration-300" style="height: {{ $height }}%"></div>
            <span class="text-xs text-gray-700 mt-1 font-semibold">{{ $hour }}h</span>

            <!-- Views count displayed above bar -->
            <span class="absolute -top-5 text-xs font-bold text-purple-600 opacity-0 group-hover:opacity-100 transition-opacity">
                {{ $views }}
            </span>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-12 gap-1 h-40">
    @foreach(range(12, 23) as $hour)
        @php
            $views = $hourlyStats[$hour] ?? 0;
            $height = ($views / $maxHourViews) * 100;
        @endphp
        <div class="flex flex-col items-center justify-end relative group">
            <div class="bg-purple-500 w-3 rounded-t-md transition-all duration-300" style="height: {{ $height }}%"></div>
            <span class="text-xs text-gray-700 mt-1 font-semibold">{{ $hour }}h</span>

            <!-- Views count displayed above bar -->
            <span class="absolute -top-5 text-xs font-bold text-purple-600 opacity-0 group-hover:opacity-100 transition-opacity">
                {{ $views }}
            </span>
        </div>
    @endforeach
</div>

<p class="text-sm text-gray-500 mt-2">Number of views per hour (hover over bars to see exact count)</p>

                         
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Engagement Rate</h4>

<div class="space-y-3">

    @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $periodKey => $label)
        @php
            $engagement = $analytics[$periodKey]['engagement_rate'] ?? 0;
            // Safely round average time_spent to avoid float-to-int warning
            $change = isset($analytics[$periodKey]['time_spent']['average'])
                ? round($analytics[$periodKey]['time_spent']['average'], 2)
                : 0;
            $trend = $change >= 0 ? 'up' : 'down';
        @endphp

        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md shadow-sm">
            <span class="text-gray-600 font-medium">{{ $label }}</span>

            <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-gray-900">{{ $engagement }}%</span>

                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $trend === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <i class="bi bi-arrow-{{ $trend }}-right mr-1"></i>
                    {{ abs($change) }}%
                </span>
            </div>
        </div>
    @endforeach

    {{-- Overall engagement --}}
    <div class="flex items-center justify-between mt-2 p-2 bg-indigo-50 rounded-md shadow">
        <span class="text-gray-700 font-semibold">Overall</span>
        <span class="text-lg font-bold text-gray-900">{{ round($analytics['engagement_rate'] ?? 0, 2) }}%</span>
    </div>

    <p class="text-sm text-gray-500 mt-1">Percentage of users who interacted with the ad beyond just viewing.</p>
</div>


                            <p class="text-sm text-gray-500">Percentage of users who interacted with the ad beyond viewing</p>
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
                            <div class="flex justify-between">
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
                            </div>
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
        @php
            $allDevices = ['desktop', 'mobile', 'tablet'];
            $deviceStats = collect($analytics['device_stats']['stats'] ?? []);
            $bestDevice = $analytics['device_stats']['best_device'] ?? null;

            // Ensure all devices exist with default 0 values
            $deviceStats = collect($allDevices)->mapWithKeys(function($device) use ($deviceStats) {
                return [
                    $device => [
                        'views' => $deviceStats[$device]['views'] ?? 0,
                        'clicks' => $deviceStats[$device]['clicks'] ?? 0,
                        'ctr' => $deviceStats[$device]['ctr'] ?? 0,
                    ]
                ];
            });

            $totalViews = $deviceStats->sum('views');
        @endphp

        @if($deviceStats->isNotEmpty())
            <div class="chart-container mx-auto mb-4" style="height: 150px; max-width: 250px;">
                <canvas id="deviceChart"></canvas>
            </div>

            <div class="mt-4 space-y-3">
                @foreach($deviceStats as $deviceType => $device)
                    <div class="flex items-center justify-between {{ $bestDevice === $deviceType ? 'bg-green-50 p-2 rounded-md' : '' }}">
                        <span class="text-sm font-medium text-gray-700">
                            {{ ucfirst($deviceType) }}
                            @if($bestDevice === $deviceType)
                                <i class="bi bi-star-fill text-yellow-500 ml-1" title="Best Device"></i>
                            @endif
                        </span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold">{{ $device['views'] }}</span>
                            <span class="text-xs text-gray-500">
                                ({{ $totalViews > 0 ? round(($device['views'] / $totalViews) * 100, 1) : 0 }}%)
                            </span>
                            <span class="text-xs text-gray-400 ml-2">CTR: {{ $device['ctr'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <script>
                const ctx = document.getElementById('deviceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($deviceStats->keys()->map(fn($d) => ucfirst($d))->toArray()) !!},
                        datasets: [{
                            data: {!! json_encode($deviceStats->pluck('views')->toArray()) !!},
                            backgroundColor: ['#4F46E5','#10B981','#F59E0B'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // allow smaller height
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 8 }
                            },
                        },
                    }
                });
            </script>
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
                    @if(count($analytics['geo_stats']) > 0)
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
                    @endif
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

        <!-- Edit Button -->
        <a href="{{ route('admin.ads.edit', $ad) }}" 
           class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 hover:shadow-md">
            <i class="bi bi-pencil mr-2"></i>
            Edit Advertisement
        </a>

        <!-- Toggle Status Button -->
        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" class="w-full toggle-status">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="{{ $ad->is_active ? 0 : 1 }}">
            <button type="submit"
                class="w-full inline-flex items-center justify-center px-6 py-3 border shadow-sm text-sm font-semibold rounded-xl
                    {{ $ad->is_active ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100' 
                                        : 'border-green-300 text-green-700 bg-green-50 hover:bg-green-100' }}">
                <i class="bi {{ $ad->is_active ? 'bi-pause' : 'bi-play' }} mr-2"></i>
                <span class="status-text">{{ $ad->is_active ? 'Pause Advertisement' : 'Activate Advertisement' }}</span>
            </button>
        </form>

        <!-- Delete Button -->
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
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1️⃣ Status Toggle (AJAX)
    document.querySelectorAll('form.toggle-status').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = form.querySelector('button');
            const statusText = btn.querySelector('.status-text');
            const isActiveInput = form.querySelector('input[name="is_active"]');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Toggle input value
                    isActiveInput.value = data.is_active ? 0 : 1;

                    // Update button appearance
                    if (data.is_active) {
                        btn.classList.remove('border-green-300', 'text-green-700', 'bg-green-50', 'hover:bg-green-100');
                        btn.classList.add('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100');
                        btn.querySelector('i').className = 'bi bi-pause mr-2';
                        statusText.textContent = 'Pause Advertisement';
                    } else {
                        btn.classList.remove('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100');
                        btn.classList.add('border-green-300', 'text-green-700', 'bg-green-50', 'hover:bg-green-100');
                        btn.querySelector('i').className = 'bi bi-play mr-2';
                        statusText.textContent = 'Activate Advertisement';
                    }
                } else {
                    alert('Failed to update status');
                }
            })
            .catch(() => alert('Something went wrong'));
        });
    });

    // 2️⃣ Charts Setup
    const dailyStats = @json($analytics['daily_stats'] ?? []);
    const deviceStats = @json($analytics['device_stats'] ?? []);
    const weekdayStats = @json($analytics['weekday_stats'] ?? []);
    const clickDistribution = @json($analytics['click_distribution'] ?? []);

    // Wrap each chart in try/catch to prevent errors from breaking the JS
    try {
        if (dailyStats && dailyStats.length > 0) {
            const performanceCtx = document.getElementById('performanceChart').getContext('2d');
            new Chart(performanceCtx, { /* your performance chart config */ });
        }
    } catch(e) { console.error(e); }

    try {
        if (deviceStats && deviceStats.length > 0) {
            const deviceCtx = document.getElementById('deviceChart')?.getContext('2d');
            if(deviceCtx) new Chart(deviceCtx, { /* your device chart config */ });
        }
    } catch(e) { console.error(e); }

    try {
        if (weekdayStats && weekdayStats.length > 0) {
            const weekdayCtx = document.getElementById('weekdayChart')?.getContext('2d');
            if(weekdayCtx) new Chart(weekdayCtx, { /* your weekday chart config */ });
        }
    } catch(e) { console.error(e); }

    try {
        if (clickDistribution && clickDistribution.length > 0) {
            const clickDistCtx = document.getElementById('clickDistributionChart')?.getContext('2d');
            if(clickDistCtx) new Chart(clickDistCtx, { /* your click distribution chart config */ });
        }
    } catch(e) { console.error(e); }

});
</script>

@endpush