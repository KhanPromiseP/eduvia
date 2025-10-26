@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Users/Cources analytics</h1>

    <!-- ADD THIS SECTION: Course and User Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Courses Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Courses</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\Course::count() }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Published: {{ \App\Models\Course::where('is_published', true)->count() }}</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-indigo-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\User::count() }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Active accounts</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Enrollments Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Enrollments</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\UserCourse::count() }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Course enrollments</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-semibold text-gray-900">${{ number_format(\App\Models\Payment::where('status', 'completed')->sum('amount'), 2) }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">From completed payments</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF ADDED SECTION -->


    {{-- In your admin dashboard --}}
@if(auth()->user()->hasRole('admin'))
<!-- Instructor Applications Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Pending Applications -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Applications</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\InstructorApplication::where('status', 'pending')->count() }}</dd>
                        <dt class="text-xs text-gray-500 mt-1">Need review</dt>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('admin.instructors.applications') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-500">
                Review applications
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Approved Instructors -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Instructors</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\Instructor::where('is_verified', true)->count() }}</dd>
                        <dt class="text-xs text-gray-500 mt-1">Teaching on platform</dt>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('admin.instructors.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                View all instructors
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Total Students/Learners -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\User::has('courses')->count() }}</dd>
                        <dt class="text-xs text-gray-500 mt-1">Enrolled in courses</dt>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                View all students
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Users Not Enrolled -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-gray-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Users Not Enrolled</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\User::doesntHave('courses')->count() }}</dd>
                        <dt class="text-xs text-gray-500 mt-1">No courses yet</dt>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-500">
                View all users
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>



    <!-- Enrollment Rate -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Enrollment Rate</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            @php
                                $totalUsers = \App\Models\User::count();
                                $enrolledUsers = \App\Models\User::has('courses')->count();
                                $enrollmentRate = $totalUsers > 0 ? round(($enrolledUsers / $totalUsers) * 100) : 0;
                            @endphp
                            {{ $enrollmentRate }}%
                        </dd>
                        <dt class="text-xs text-gray-500 mt-1">Of users are enrolled</dt>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Courses Per Student -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-pink-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book-open text-pink-600"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg Courses/Student</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            @php
                                $totalEnrollments = \App\Models\UserCourse::count();
                                $enrolledUsers = \App\Models\User::has('courses')->count();
                                $avgCourses = $enrolledUsers > 0 ? round($totalEnrollments / $enrolledUsers, 1) : 0;
                            @endphp
                            {{ $avgCourses }}
                        </dd>
                        <dt class="text-xs text-gray-500 mt-1">Courses per enrolled user</dt>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endif


@if(auth()->user()->hasRole('admin'))
{{-- or can use @if(auth()->user()->is_admin)  and not @if(auth()->user()->role === 'admin') since I am using spatie and not a role column --}} 
   
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Ads Analytics Dashboard</h1>
        
        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Date Range</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div>
                            <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Quick Range</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                    id="date_range" name="date_range">
                                @foreach($dateRanges as $value => $label)
                                    <option value="{{ $value }}" {{ request('date_range') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Apply Filter
                            </button>
                        


                    
                            <a href="{{ route('admin.ads.index') }}" 
                            class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="bi bi-plus-circle mr-2"></i> New Ad
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Views Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-eye text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Views</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($analytics['overview']['total_views']) }}</dd>
                                <dt class="text-xs text-gray-500 mt-1">
                                    @if($analytics['overview']['views_change'] > 0)
                                        <span class="text-green-600"><i class="fas fa-arrow-up"></i> {{ $analytics['overview']['views_change'] }}%</span>
                                    @elseif($analytics['overview']['views_change'] < 0)
                                        <span class="text-red-600"><i class="fas fa-arrow-down"></i> {{ abs($analytics['overview']['views_change']) }}%</span>
                                    @else
                                        <span class="text-gray-500">No change</span>
                                    @endif
                                    from previous period
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Clicks Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-mouse-pointer text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clicks</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($analytics['overview']['total_clicks']) }}</dd>
                                <dt class="text-xs text-gray-500 mt-1">
                                    @if($analytics['overview']['clicks_change'] > 0)
                                        <span class="text-green-600"><i class="fas fa-arrow-up"></i> {{ $analytics['overview']['clicks_change'] }}%</span>
                                    @elseif($analytics['overview']['clicks_change'] < 0)
                                        <span class="text-red-600"><i class="fas fa-arrow-down"></i> {{ abs($analytics['overview']['clicks_change']) }}%</span>
                                    @else
                                        <span class="text-gray-500">No change</span>
                                    @endif
                                    from previous period
                                </dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTR Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-cyan-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-percent text-cyan-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">CTR</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $analytics['overview']['ctr'] }}%</dd>
                                <dt class="text-xs text-gray-500 mt-1">Click-through rate</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Avg. Time Spent Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg. Time Spent</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $analytics['overview']['avg_time_spent'] }}s</dd>
                                <dt class="text-xs text-gray-500 mt-1">Per view</dt>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Trends Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Performance Trends</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Device Breakdown</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="chart-container" style="position: relative; height:250px;">
                            <canvas id="deviceChart"></canvas>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            <div class="flex flex-wrap justify-center gap-3">
                                @foreach($analytics['device_breakdown'] as $device => $data)
                                    <span class="inline-flex items-center">
                                        <span class="w-3 h-3 rounded-full 
                                            @if($device == 'Desktop') bg-blue-500
                                            @elseif($device == 'Mobile') bg-green-500
                                            @else bg-cyan-500
                                            @endif mr-1"></span>
                                        {{ $device }} ({{ $data['percentage'] }}%)
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection



@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Custom styles for better mobile experience */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .text-lg {
        font-size: 1.125rem;
    }
    
    .text-2xl {
        font-size: 1.5rem;
    }
}

/* Chart responsiveness */
.chart-container {
    position: relative;
    width: 100%;
}

/* Card hover effects */
.bg-white {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Form element styling */
input, select {
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

input:focus, select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button styling */
button {
    transition: background-color 0.2s ease;
}

/* Responsive text sizing */
@media (max-width: 768px) {
    .text-sm {
        font-size: 0.75rem;
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing charts...');
    
    // Debug: Check if analytics data is available
    console.log('Analytics data:', {!! json_encode($analytics) !!});

    // Performance Trends Chart - FIXED VERSION
    const performanceCtx = document.getElementById('performanceChart');
    
    if (performanceCtx) {
        console.log('Initializing performance chart...');
        
        // Prepare data for the chart - FIXED FORMAT
        const viewsData = {!! json_encode($analytics['performance_trends']['views']) !!};
        const clicksData = {!! json_encode($analytics['performance_trends']['clicks']) !!};
        
        // Convert to proper format for Chart.js
        const viewsDataset = [];
        const clicksDataset = [];
        
        Object.keys(viewsData).forEach(date => {
            viewsDataset.push({
                x: moment(date).format('YYYY-MM-DD'),
                y: viewsData[date]
            });
        });
        
        Object.keys(clicksData).forEach(date => {
            clicksDataset.push({
                x: moment(date).format('YYYY-MM-DD'),
                y: clicksData[date]
            });
        });
        
        console.log('Views dataset:', viewsDataset);
        console.log('Clicks dataset:', clicksDataset);

        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: "Views",
                        tension: 0.3,
                        backgroundColor: "rgba(59, 130, 246, 0.05)",
                        borderColor: "rgba(59, 130, 246, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(59, 130, 246, 1)",
                        pointBorderColor: "rgba(59, 130, 246, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(59, 130, 246, 1)",
                        pointHoverBorderColor: "rgba(59, 130, 246, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: viewsDataset,
                        fill: true,
                    },
                    {
                        label: "Clicks",
                        tension: 0.3,
                        backgroundColor: "rgba(16, 185, 129, 0.05)",
                        borderColor: "rgba(16, 185, 129, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(16, 185, 129, 1)",
                        pointBorderColor: "rgba(16, 185, 129, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(16, 185, 129, 1)",
                        pointHoverBorderColor: "rgba(16, 185, 129, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: clicksDataset,
                        fill: true,
                    }
                ],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'MMM D, YYYY',
                            displayFormats: {
                                day: 'MMM D'
                            }
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7,
                            maxRotation: 0,
                            autoSkip: true
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgba(255, 255, 255, 0.95)",
                        bodyColor: "#374151",
                        titleColor: '#111827',
                        titleMarginBottom: 10,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            title: function(context) {
                                return moment(context[0].parsed.x).format('MMM D, YYYY');
                            },
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        console.log('Performance chart initialized successfully');
    } else {
        console.error('Performance chart canvas not found');
    }

    // Device Breakdown Chart - SIMPLIFIED VERSION
    const deviceCtx = document.getElementById('deviceChart');
    
    if (deviceCtx) {
        console.log('Initializing device chart...');
        
        const deviceLabels = {!! json_encode(array_keys($analytics['device_breakdown'])) !!};
        const deviceData = {!! json_encode(array_column($analytics['device_breakdown'], 'percentage')) !!};
        
        console.log('Device labels:', deviceLabels);
        console.log('Device data:', deviceData);

        // Create a simple pie chart instead of doughnut for better visibility
        const deviceChart = new Chart(deviceCtx, {
            type: 'pie', // Changed from doughnut to pie for better visibility
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceData,
                    backgroundColor: ['#3b82f6', '#10b981', '#06b6d4', '#f59e0b', '#ef4444'],
                    hoverBackgroundColor: ['#2563eb', '#059669', '#0891b2', '#d97706', '#dc2626'],
                    hoverBorderColor: "rgba(255, 255, 255, 0.8)",
                    borderWidth: 2,
                    hoverOffset: 15,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true, // Changed to true to show legend
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgba(255, 255, 255, 0.95)",
                        bodyColor: "#374151",
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value}% (${percentage}% of total)`;
                            }
                        }
                    }
                }
            },
        });
        
        console.log('Device chart initialized successfully');
    } else {
        console.error('Device chart canvas not found');
    }
});
</script>
@endpush