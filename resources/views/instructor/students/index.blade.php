@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">My Students</h1>
        <div class="text-sm text-gray-600">
            Total Students: <span class="font-semibold">{{ $students->count() }}</span>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       placeholder="Search students by name or email..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex space-x-2">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Courses</option>
                    @foreach(Auth::user()->courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    @if($students->count() > 0)
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Courses Enrolled
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                            @php
                                $latestEnrollment = $student->userCourses->sortByDesc('purchased_at')->first();
                                $enrolledCourses = $student->userCourses->pluck('course.title')->toArray();
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if(!empty($student->profile_path))
                                                <img class="h-10 w-10 rounded-full object-cover border border-indigo-200"
                                                    src="{{ asset('storage/' . $student->profile_path) }}"
                                                    alt="{{ $student->name }}">
                                            @else
                                                <img class="h-10 w-10 rounded-full object-cover border border-indigo-200"
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random"
                                                    alt="{{ $student->name }}">
                                            @endif
                                        </div>

                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $student->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ $student->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                    @if($student->phone)
                                        <div class="text-xs text-gray-500">{{ $student->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-semibold">{{ $student->user_courses_count }}</span> courses
                                    </div>
                                    @if(count($enrolledCourses) > 0)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ implode(', ', array_slice($enrolledCourses, 0, 2)) }}
                                            @if(count($enrolledCourses) > 2)
                                                +{{ count($enrolledCourses) - 2 }} more
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($latestEnrollment)
                                        {{ $latestEnrollment->purchased_at->format('M j, Y') }}
                                        <div class="text-xs text-gray-400">
                                            {{ $latestEnrollment->purchased_at->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">No activity</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('instructor.students.detail', $student->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 transition"
                                           title="View Student Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="mailto:{{ $student->email }}" 
                                           class="text-blue-600 hover:text-blue-900 transition"
                                           title="Send Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $students->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Avg Courses per Student</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ number_format($students->avg('user_courses_count'), 1) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active This Month</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $students->filter(function($student) {
                                return $student->userCourses->contains('purchased_at', '>=', now()->subMonth());
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Yet</h3>
                <p class="text-gray-500 mb-6">
                    You don't have any students enrolled in your courses yet. 
                    Share your courses to attract students!
                </p>
                <div class="space-y-3">
                    <a href="{{ route('instructor.courses.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-book mr-2"></i>
                        View My Courses
                    </a>
                    <a href="{{ route('instructor.courses.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 ml-3">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Course
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush