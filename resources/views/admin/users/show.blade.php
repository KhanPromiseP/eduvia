@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Users
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">User Details</h1>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Member Since</p>
            <p class="text-sm font-semibold text-gray-900">
                {{ $user->created_at->format('M j, Y') }}
            </p>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    @if($user->profile_path)
                        <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-200"
                            src="{{ asset('storage/' . $user->profile_path) }}"
                            alt="{{ $user->name }}">
                    @else
                        <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-200"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&size=128"
                            alt="{{ $user->name }}">
                    @endif
                    <div>
                        <div class="flex items-center">
                            <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                            @if($user->hasRole('admin'))
                                <i class="fas fa-crown text-yellow-500 ml-2 text-lg" title="Admin"></i>
                            @endif
                        </div>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-gray-500 text-sm">{{ $user->phone }}</p>
                        @endif
                        <p class="text-gray-500 text-sm mt-1">User ID: {{ $user->id }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i> Edit User
                    </a>
                    <a href="mailto:{{ $user->email }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-envelope mr-2"></i> Send Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Courses Enrolled</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->courses->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Courses Created</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->courses_created_count ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">User Roles</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->getRoleNames()->count() }}</p>
                </div>
            </div>
        </div>

       
    </div>

    <!-- User Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Full Name</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email Address</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone Number</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $user->phone ?? 'Not provided' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Last Login</label>
                    <p class="text-sm text-gray-900 mt-1">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Roles & Permissions</h3>
            <div class="space-y-3">
                @foreach($user->getRoleNames() as $role)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $role }}</span>
                        @if($role === 'admin')
                            <i class="fas fa-crown text-yellow-500" title="Administrator"></i>
                        @elseif($role === 'instructor')
                            <i class="fas fa-chalkboard-teacher text-blue-500" title="Instructor"></i>
                        @else
                            <i class="fas fa-user-graduate text-green-500" title="Student"></i>
                        @endif
                    </div>
                @endforeach
                
                @if($user->getRoleNames()->count() === 0)
                    <p class="text-sm text-gray-500 text-center py-4">No roles assigned</p>
                @endif
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.users.edit', $user) }}?tab=roles" 
                   class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-edit mr-1"></i> Manage Roles
                </a>
            </div>
        </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Enrolled Courses</h3>
            <span class="text-sm text-gray-500">{{ $user->courses->count() }} courses</span>
        </div>
        
        @if($user->courses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purchased
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->courses as $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($course->image)
                                            <img class="h-10 w-10 rounded-lg object-cover mr-3" 
                                                 src="{{ asset('storage/' . $course->image) }}" 
                                                 alt="{{ $course->title }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                                <i class="fas fa-book text-indigo-600"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $course->title }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $course->category->name ?? 'Uncategorized' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $course->instructor->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($course->pivot->amount_paid ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $course->pivot->purchased_at->format('M j, Y') }}
                                    <div class="text-xs text-gray-500">
                                        {{ $course->pivot->purchased_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($course->pivot->completed_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            In Progress
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
                    This user hasn't enrolled in any courses yet.
                </p>
            </div>
        @endif
    </div>

    <!-- User Activity (Optional) -->
    <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-chart-line text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500">Activity tracking will be implemented soon</p>
                <p class="text-sm text-gray-400 mt-1">User login history, course progress, and other activities will appear here</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush