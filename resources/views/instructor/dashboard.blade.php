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
    

    // Performance Trends Chart - FIXED VERSION
    const performanceCtx = document.getElementById('performanceChart');

});
</script>
@endpush