@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900">Manage Courses</h3>
                <a href="{{ route('admin.courses.create') }}"
                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add New Course
                </a>
            </div>

            <!-- Table (Desktop) -->
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Modules</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Approval Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Visibility</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <!-- Title -->
                                <td class="px-4 py-3 whitespace-nowrap flex items-center">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-10 w-10 rounded-full object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-indigo-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    @if($course->is_premium)
                                        ${{ number_format($course->price, 2) }}
                                    @else
                                        <span class="text-green-600 font-medium">Free</span>
                                    @endif
                                </td>

                                <!-- Modules -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->modules_count }} modules
                                </td>

                                <!-- Approval Status -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $approvalStatusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending_review' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800'
                                        ];
                                        $approvalStatusLabels = [
                                            'draft' => 'Draft',
                                            'pending_review' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $approvalStatusColors[$course->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $approvalStatusLabels[$course->status] ?? $course->status }}
                                    </span>
                                </td>

                                <!-- Publication Status -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($course->isApproved())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_published ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            N/A
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <!-- View Course -->
                                        <a href="{{ route('admin.courses.show', $course) }}" 
                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded" 
                                        aria-label="View" 
                                        title="View Course Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Course -->
                                        <a href="{{ route('admin.courses.edit', $course) }}" 
                                        class="text-blue-600 hover:text-blue-900 p-1 rounded" 
                                        aria-label="Edit" 
                                        title="Edit Course">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Manage Modules -->
                                        <a href="{{ route('admin.courses.modules', $course) }}" 
                                        class="text-purple-600 hover:text-purple-900 p-1 rounded" 
                                        aria-label="Modules" 
                                        title="Manage Modules">
                                            <i class="fas fa-layer-group"></i>
                                        </a>

                                        <!-- Publish/Unpublish Toggle (Only for approved courses) -->
                                        @if($course->isApproved())
                                            <form action="{{ route('admin.courses.toggle-publish', $course) }}" 
                                                method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-gray-600 hover:text-gray-900 p-1 rounded" 
                                                        aria-label="Toggle Publish" 
                                                        title="{{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}">
                                                    <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Review Actions for Pending Courses -->
                                        @if($course->isPendingReview())
                                            <!-- Quick Approve -->
                                            <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 p-1 rounded" 
                                                        aria-label="Approve" 
                                                        title="Approve Course"
                                                        onclick="return confirm('Approve this course?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <!-- Quick Reject -->
                                            <button type="button" 
                                                    onclick="showRejectModal({{ $course->id }})"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded" 
                                                    aria-label="Reject" 
                                                    title="Reject Course">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <!-- Delete Course -->
                                        <form action="{{ route('admin.courses.destroy', $course) }}" 
                                            method="POST" 
                                            onsubmit="return confirmDelete()" 
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded" 
                                                    aria-label="Delete" 
                                                    title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal for each course -->
                            @if($course->isPendingReview())
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
                                                <label for="review_notes-{{ $course->id }}" class="block text-sm font-medium text-gray-700 text-left mb-2">
                                                    Reason for Rejection
                                                </label>
                                                <textarea name="review_notes" id="review_notes-{{ $course->id }}" 
                                                          rows="4" 
                                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                                          placeholder="Please provide specific feedback for the instructor..."
                                                          required></textarea>
                                            </div>
                                            <div class="flex justify-end space-x-3">
                                                <button type="button" 
                                                        onclick="hideRejectModal({{ $course->id }})"
                                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                                                    Cancel
                                                </button>
                                                <button type="submit" 
                                                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                                    Confirm Reject
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                    No courses found. <a href="{{ route('admin.courses.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first course</a>.
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
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($course->description, 60) }}</div>
                                
                                <div class="text-xs text-gray-400 mt-1">
                                    By {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                </div>

                                <!-- Status Info -->
                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-600">Price:</span>
                                        @if($course->is_premium)
                                            <span class="text-gray-900">${{ number_format($course->price, 2) }}</span>
                                        @else
                                            <span class="text-green-600 font-medium">Free</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">Modules:</span>
                                        <span class="text-gray-900">{{ $course->modules_count }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">Approval:</span>
                                        @php
                                            $approvalStatusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'pending_review' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800'
                                            ];
                                            $approvalStatusLabels = [
                                                'draft' => 'Draft',
                                                'pending_review' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $approvalStatusColors[$course->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $approvalStatusLabels[$course->status] ?? $course->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">Visibility:</span>
                                        @if($course->isApproved())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_published ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs">N/A</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Mobile Actions -->
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.courses.show', $course) }}" 
                                       class="flex-1 bg-indigo-600 text-white text-center py-2 rounded text-sm hover:bg-indigo-700 transition">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    
                                    <a href="{{ route('admin.courses.edit', $course) }}" 
                                       class="flex-1 bg-blue-600 text-white text-center py-2 rounded text-sm hover:bg-blue-700 transition">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    <a href="{{ route('admin.courses.modules', $course) }}" 
                                       class="flex-1 bg-purple-600 text-white text-center py-2 rounded text-sm hover:bg-purple-700 transition">
                                        <i class="fas fa-layer-group mr-1"></i> Modules
                                    </a>

                                    @if($course->isApproved())
                                    <form action="{{ route('admin.courses.toggle-publish', $course) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full {{ $course->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 rounded text-sm transition">
                                            <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    @endif

                                    @if($course->isPendingReview())
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-4 bg-white border-t border-gray-200 flex justify-center">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this course? This action cannot be undone.');
}

function showRejectModal(courseId) {
    const modal = document.getElementById('reject-modal-' + courseId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function hideRejectModal(courseId) {
    const modal = document.getElementById('reject-modal-' + courseId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-gray-600')) {
        const modals = document.querySelectorAll('[id^="reject-modal-"]');
        modals.forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
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