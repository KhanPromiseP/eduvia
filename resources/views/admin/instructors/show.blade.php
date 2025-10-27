@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="flex items-center space-x-4 mb-4 lg:mb-0">
            <!-- Instructor Avatar -->
            <div class="flex-shrink-0">
                @if($instructor->user->profile_path)
                    <img class="h-16 w-16 rounded-full object-cover border-4 border-white shadow-lg" 
                         src="{{ asset('storage/' . $instructor->user->profile_path) }}" 
                         alt="{{ $instructor->user->name }}">
                @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg">
                        {{ strtoupper(substr($instructor->user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                    {{ $instructor->user->name }}
                    @if($instructor->is_verified)
                        <i class="fas fa-check-circle text-blue-500 ml-2 text-lg" title="Verified Instructor"></i>
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">{{ $instructor->headline ?? 'Professional Instructor' }}</p>
                <div class="flex items-center space-x-4 mt-2">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-envelope mr-1"></i>{{ $instructor->user->email }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>Joined {{ $instructor->created_at->format('M j, Y') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('instructor.profile', $instructor->user_id) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                <i class="fas fa-external-link-alt mr-2"></i>
                View Public Profile
            </a>
            <a href="{{ route('admin.instructors.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Badges -->
    <div class="flex flex-wrap gap-3 mb-6">
        <span class="px-3 py-1 rounded-full text-sm font-semibold 
            @if($instructor->is_verified) bg-green-100 text-green-800
            @else bg-yellow-100 text-yellow-800 @endif">
            <i class="fas fa-badge-check mr-1"></i>
            {{ $instructor->is_verified ? 'Verified' : 'Unverified' }}
        </span>
        <span class="px-3 py-1 rounded-full text-sm font-semibold 
            @if($instructor->isSuspended()) bg-red-100 text-red-800
            @elseif($instructor->isActive()) bg-green-100 text-green-800
            @else bg-yellow-100 text-yellow-800 @endif">
            <i class="fas fa-user mr-1"></i>
            @if($instructor->isSuspended()) Suspended
            @elseif($instructor->isActive()) Active
            @else Inactive @endif
        </span>
        @if($instructor->isSuspended())
        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-50 text-red-700 border border-red-200">
            <i class="fas fa-ban mr-1"></i>
            Suspended on {{ $instructor->suspended_at->format('M j, Y') }}
        </span>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column - Profile & Stats -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Courses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $stats['published_courses'] ?? 0 }} published • {{ $stats['draft_courses'] ?? 0 }} draft
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students'] ?? 0) }}</p>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $stats['recent_students'] ?? 0 }} new this month
                    </div>
                </div>

                <!-- Total Earnings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_earnings'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        ${{ number_format($stats['pending_earnings'] ?? 0, 2) }} pending
                    </div>
                </div>

                <!-- Average Rating -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}/5</p>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $stats['total_reviews'] ?? 0 }} reviews
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                        Profile Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Bio</h3>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-4 min-h-[100px]">
                                {{ $instructor->bio ?? 'No biography provided.' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Expertise & Skills</h3>
                            <div class="space-y-3">
                                @if($instructor->skills && count($instructor->skills) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($instructor->skills as $skill)
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">No skills listed</p>
                                @endif
                                
                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 mb-1">Languages</h4>
                                    @if($instructor->languages && count($instructor->languages) > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($instructor->languages as $language)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                                    {{ $language }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-xs">No languages specified</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-book-open text-green-500 mr-2"></i>
                        Recent Courses
                    </h2>
                    <span class="text-sm text-gray-500">{{ $instructor->courses->count() }} total courses</span>
                </div>
                <div class="p-6">
                    @if($instructor->courses->count() > 0)
                        <div class="space-y-4">
                            @foreach($instructor->courses->take(5) as $course)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center space-x-4">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="w-16 h-12 object-cover rounded">
                                        @else
                                            <div class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                <i class="fas fa-book text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                                <span><i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }} students</span>
                                                <span><i class="fas fa-star text-yellow-400 mr-1"></i> {{ number_format($course->reviews_avg_rating ?? 0, 1) }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($course->status === 'published') bg-green-100 text-green-800
                                                    @elseif($course->status === 'draft') bg-gray-100 text-gray-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($course->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">${{ number_format($course->price, 2) }}</p>
                                        <p class="text-sm text-gray-500">{{ $course->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($instructor->courses->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View all {{ $instructor->courses->count() }} courses →
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-book text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No courses created yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-comments text-yellow-500 mr-2"></i>
                        Recent Reviews
                    </h2>
                </div>
                <div class="p-6">
                    @if($recentReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentReviews as $review)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-3">
                                            @if($review->user->profile_path)
                                                <img src="{{ asset('storage/' . $review->user->profile_path) }}" 
                                                     alt="{{ $review->user->name }}" 
                                                     class="w-10 h-10 rounded-full">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium">{{ strtoupper(substr($review->user->name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                                <p class="text-sm text-gray-500">on {{ $review->course->title }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                @else
                                                    <i class="far fa-star text-yellow-400 text-sm"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                    <div class="flex justify-between items-center mt-3 text-sm text-gray-500">
                                        <span>{{ $review->created_at->format('M j, Y \\a\\t g:i A') }}</span>
                                        @if($review->instructor_response)
                                            <span class="text-green-600 font-medium">Replied</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-comment-slash text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No reviews yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt text-purple-500 mr-2"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-4 space-y-3">
                    @if(!$instructor->isSuspended())
                        <button onclick="openSuspendModal({{ $instructor->id }})"
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition">
                            <i class="fas fa-ban mr-2"></i>
                            Suspend Instructor
                        </button>
                    @else
                        <form action="{{ route('admin.instructors.reactivate', $instructor) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 transition">
                                <i class="fas fa-check-circle mr-2"></i>
                                Reactivate Instructor
                            </button>
                        </form>
                    @endif
                    
                    <a href="mailto:{{ $instructor->user->email }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Email
                    </a>
                    
                    <button class="w-full flex items-center justify-center px-4 py-2 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Earnings Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-green-500 mr-2"></i>
                        Earnings Overview
                    </h2>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Earnings</span>
                        <span class="font-bold text-gray-900">${{ number_format($stats['total_earnings'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending Payout</span>
                        <span class="font-bold text-yellow-600">${{ number_format($stats['pending_earnings'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Lifetime Earnings</span>
                        <span class="font-bold text-purple-600">${{ number_format($stats['lifetime_earnings'] ?? 0, 2) }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium w-full text-center block">
                            View Detailed Earnings Report →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payout Information -->
            @if($instructor->payouts)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-wallet text-blue-500 mr-2"></i>
                        Payout Method
                    </h2>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Method</span>
                        <span class="text-sm font-medium text-gray-900 capitalize">
                            @if($instructor->payouts->payout_method === 'mobile_money')
                                <i class="fas fa-mobile-alt mr-1"></i>Mobile Money
                            @elseif($instructor->payouts->payout_method === 'bank_account')
                                <i class="fas fa-university mr-1"></i>Bank Transfer
                            @else
                                <i class="fas fa-wallet mr-1"></i>Tranzak Wallet
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Account Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ $instructor->payouts->account_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Account Number</span>
                        <span class="text-sm font-medium text-gray-900">{{ $instructor->payouts->account_number }}</span>
                    </div>
                    @if($instructor->payouts->operator)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Operator/Bank</span>
                        <span class="text-sm font-medium text-gray-900">{{ $instructor->payouts->operator }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200">
                        <span class="text-sm {{ $instructor->payouts->is_verified ? 'text-green-600' : 'text-yellow-600' }}">
                            <i class="fas fa-{{ $instructor->payouts->is_verified ? 'check-circle' : 'clock' }} mr-1"></i>
                            {{ $instructor->payouts->is_verified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Performance Metrics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tachometer-alt text-orange-500 mr-2"></i>
                        Performance
                    </h2>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Course Completion Rate</span>
                            <span class="font-medium text-gray-900">{{ $stats['completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Student Engagement</span>
                            <span class="font-medium text-gray-900">{{ $stats['total_followers'] ?? 0 }} followers</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(($stats['total_followers'] ?? 0) * 2, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_course_rating'] ?? 0, 1) }}</div>
                            <div class="text-xs text-gray-500">Avg Course Rating</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_reviews'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Total Reviews</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-ban text-red-600 text-xl"></i>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Suspend Instructor</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to suspend <strong>{{ $instructor->user->name }}</strong>? They will lose access to instructor privileges.
                    </p>
                </div>
                <div class="mt-4">
                    <form id="suspendForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="suspend_reason" class="block text-sm font-medium text-gray-700 mb-1 text-left">
                                Reason for Suspension *
                            </label>
                            <textarea name="reason" id="suspend_reason" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                                placeholder="Provide reason for suspension..." required></textarea>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button type="button" onclick="closeSuspendModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Suspend Instructor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openSuspendModal(instructorId) {
    const modal = document.getElementById('suspendModal');
    const form = document.getElementById('suspendForm');
    form.action = `/admin/instructors/${instructorId}/suspend`;
    modal.classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('suspendModal');
    if (event.target === modal) {
        closeSuspendModal();
    }
}
</script>

<style>
.hover-lift:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}
</style>
@endpush