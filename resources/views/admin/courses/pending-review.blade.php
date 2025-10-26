@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Courses Pending Review</h3>
                    <p class="text-sm text-gray-500 mt-1">Review and approve or reject submitted courses</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.courses.index') }}"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Courses
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="rounded-full bg-yellow-100 p-3">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Pending Review</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $courses->total() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Instructors</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $instructorsCount }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Approved This Week</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $approvedThisWeek }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="rounded-full bg-red-100 p-3">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Rejected This Week</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $rejectedThisWeek }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table (Desktop) -->
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Course & Instructor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Modules</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <!-- Course & Instructor -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-10 w-10 rounded-full object-cover mr-3">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                <i class="fas fa-book text-indigo-600"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                            <div class="text-sm text-gray-500">
                                                By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ Str::limit($course->description, 60) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </td>

                                <!-- Modules -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <span class="mr-2">{{ $course->modules_count }} modules</span>
                                        @if($course->modules_count > 0)
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                {{ $course->modules->where('is_free', true)->count() }} free
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Submitted -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->updated_at->diffForHumans() }}
                                    <div class="text-xs text-gray-400">
                                        {{ $course->updated_at->format('M d, Y') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- View Course -->
                                        <a href="{{ route('admin.courses.show', $course) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded text-xs font-medium"
                                           title="View Course Details">
                                            <i class="fas fa-eye mr-1"></i> Review
                                        </a>

                                        <!-- Quick Approve -->
                                        <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded text-xs font-medium"
                                                    title="Approve Course"
                                                    onclick="return confirm('Approve this course?')">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Quick Reject -->
                                        <button type="button" 
                                                onclick="showRejectModal({{ $course->id }})"
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded text-xs font-medium"
                                                title="Reject Course">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal (Hidden by default) -->
                            <div id="reject-modal-{{ $course->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                    <div class="mt-3 text-center">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mt-2">Reject Course</h3>
                                        <form action="{{ route('admin.courses.reject', $course) }}" method="POST" class="mt-4">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="rejection_reason-{{ $course->id }}" class="block text-sm font-medium text-gray-700 text-left mb-2">
                                                    Reason for Rejection
                                                </label>
                                                <textarea name="rejection_reason" id="rejection_reason-{{ $course->id }}" 
                                                          rows="3" 
                                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                                                          placeholder="Please provide specific feedback for the instructor..."
                                                          required></textarea>
                                            </div>
                                            <div class="flex justify-end space-x-3">
                                                <button type="button" 
                                                        onclick="hideRejectModal({{ $course->id }})"
                                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">
                                                    Cancel
                                                </button>
                                                <button type="submit" 
                                                        class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700 transition">
                                                    Confirm Reject
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                                        <p class="text-lg">No courses pending review!</p>
                                        <p class="text-sm mt-1">All submitted courses have been reviewed.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (sm:hidden) -->
            <div class="sm:hidden px-4 space-y-4 mt-6">
                @foreach ($courses as $course)
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <div class="flex items-start space-x-3">
                            <!-- Image -->
                            <div class="flex-shrink-0">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <i class="fas fa-book"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-grow min-w-0">
                                <div class="text-base font-semibold text-gray-900">{{ $course->title }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                </div>
                                <div class="text-sm text-gray-600 mt-2">
                                    {{ Str::limit($course->description, 80) }}
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Category:</span>
                                        {{ $course->category->name ?? 'Uncategorized' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Modules:</span>
                                        {{ $course->modules_count }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Submitted:</span>
                                        {{ $course->updated_at->diffForHumans() }}
                                    </div>
                                </div>

                                <!-- Mobile Actions -->
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.courses.show', $course) }}" 
                                       class="flex-1 bg-indigo-600 text-white text-center py-2 rounded text-sm hover:bg-indigo-700 transition">
                                        <i class="fas fa-eye mr-1"></i> Review
                                    </a>
                                    <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-green-600 text-white py-2 rounded text-sm hover:bg-green-700 transition"
                                                onclick="return confirm('Approve this course?')">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" 
                                            onclick="showRejectModal({{ $course->id }})"
                                            class="flex-1 bg-red-600 text-white py-2 rounded text-sm hover:bg-red-700 transition">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
            <div class="px-4 py-4 bg-white border-t border-gray-200 flex justify-center">
                {{ $courses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function showRejectModal(courseId) {
    const modal = document.getElementById('reject-modal-' + courseId);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideRejectModal(courseId) {
    const modal = document.getElementById('reject-modal-' + courseId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-gray-600')) {
        event.target.classList.add('hidden');
        event.target.classList.remove('flex');
    }
});
</script>

<style>
.fixed {
    position: fixed;
}
.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
.z-50 {
    z-index: 50;
}
</style>
@endsection