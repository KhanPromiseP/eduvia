@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">My Instructor Analytics</h1>

    <!-- Instructor Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- My Courses Card -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">My Courses</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_courses'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Published: {{ $stats['published_courses'] }}</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Students Card -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">My Students</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_students'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Unique students enrolled</dt>
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
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_enrollments'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Course enrollments</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Revenue Card -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">My Revenue</dt>
                            <dd class="text-lg font-semibold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">From my courses</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Instructor Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Course Performance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Performance</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Rating</span>
                    <span class="text-lg font-semibold text-indigo-600">
                        {{ number_format($stats['average_rating'], 1) }} ⭐
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Student Engagement</span>
                    <span class="text-lg font-semibold text-green-600">
                        {{ $stats['total_students'] > 0 ? number_format(($stats['total_enrollments'] / $stats['total_students']), 1) : 0 }}x
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('instructor.courses.create') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Create New Course
                </a>
                <a href="{{ route('instructor.courses.index') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-book mr-2"></i> Manage Courses
                </a>
                <a href="{{ route('instructor.students') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-users mr-2"></i> View Students
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Placeholder -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <p class="text-gray-500 text-center py-4">Recent student enrollments and course activity will appear here.</p>
        <!-- You can add more detailed activity feed here -->
    </div>
</div>
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

/* Card hover effects */
.bg-white {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endpush