@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <a href="{{ route('instructor.students') }}" 
               class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Students
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Student Details</h1>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Student Since</p>
            <p class="text-sm font-semibold text-gray-900">
                {{ $student->created_at->format('M j, Y') }}
            </p>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                     @if(!empty($student->profile_path))
                        <img class="h-12 w-12 rounded-full object-cover border border-indigo-200"
                                src="{{ asset('storage/' . $student->profile_path) }}"
                                alt="{{ $student->name }}">
                        @else
                            <img class="h-12 w-12 rounded-full object-cover border border-indigo-200"
                                src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random"
                                alt="{{ $student->name }}">
                        @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $student->name }}</h2>
                        <p class="text-gray-600">{{ $student->email }}</p>
                        @if($student->phone)
                            <p class="text-gray-500 text-sm">{{ $student->phone }}</p>
                        @endif
                        <p class="text-gray-500 text-sm mt-1">User ID: {{ $student->id }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="mailto:{{ $student->email }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-envelope mr-2"></i> Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Courses Enrolled</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $totalCoursesEnrolled }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $completedCourses }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Time Spent</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ formatTimeSpent($totalTimeSpent) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $totalCoursesEnrolled > 0 ? number_format(($completedCourses / $totalCoursesEnrolled) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

   <!-- Enrolled Courses -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Enrolled Courses</h3>
    </div>
    
    @if($student->userCourses->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enrollment Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Time Spent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($student->userCourses as $enrollment)
                        @php
                            // Use the pre-calculated progress data from controller
                            $completionPercentage = $enrollment->completion_percentage ?? 0;
                            $timeSpent = $enrollment->time_spent ?? 0;
                            $completedModules = $enrollment->completed_modules ?? 0;
                            $totalModules = $enrollment->total_modules ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($enrollment->course->image)
                                        <img class="h-10 w-10 rounded-lg object-cover mr-3" 
                                             src="{{ asset('storage/' . $enrollment->course->image) }}" 
                                             alt="{{ $enrollment->course->title }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-indigo-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $enrollment->course->title }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $enrollment->course->category->name ?? 'Uncategorized' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $completedModules }}/{{ $totalModules }} modules
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $enrollment->purchased_at->format('M j, Y') }}
                                <div class="text-xs text-gray-500">
                                    {{ $enrollment->purchased_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                             style="width: {{ $completionPercentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $completionPercentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($timeSpent > 0)
                                    {{ formatTimeSpent($timeSpent) }}
                                @else
                                    <span class="text-gray-400">Not started</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($completionPercentage == 100)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($completionPercentage > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Not Started
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-gray-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Course Enrollments</h3>
            <p class="text-gray-500">
                This student hasn't enrolled in any of your courses yet.
            </p>
        </div>
    @endif
</div>

    <!-- Recent Activity (Optional) -->
    <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-center py-4">
                Activity tracking will be displayed here when implemented.
            </p>
            <!-- You can add activity logs, recent progress updates, etc. here -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush