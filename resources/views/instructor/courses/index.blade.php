@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-indigo-100 p-3">
                        <i class="fas fa-book text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-gray-100 p-3">
                        <i class="fas fa-edit text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Draft</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-yellow-100 p-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Review</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_review'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-green-100 p-3">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-blue-100 p-3">
                        <i class="fas fa-eye text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Published</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900">My Courses</h3>
                <a href="{{ route('instructor.courses.create') }}"
                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Create New Course
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
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

                                <!-- Status -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending_review' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'published' => 'bg-blue-100 text-blue-800',
                                            'rejected' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Draft',
                                            'pending_review' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'published' => 'Published',
                                            'rejected' => 'Rejected'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$course->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$course->status] ?? $course->status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <!-- View Button -->
                                        <a href="{{ route('instructor.courses.show', $course) }}" 
                                        class="text-indigo-600 hover:text-indigo-900" 
                                        aria-label="View" 
                                        title="View Course">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Analytics Button -->
                                        <a href="{{ route('instructor.courses.analytics', $course) }}" 
                                        class="text-purple-600 hover:text-purple-900" 
                                        aria-label="Analytics" 
                                        title="View Analytics">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>

                                        <!-- Edit Button - Always Visible -->
                                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                                        class="text-blue-600 hover:text-blue-900" 
                                        aria-label="Edit" 
                                        title="Edit Course">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Modules Button -->
                                        <a href="{{ route('instructor.courses.modules', $course) }}" 
                                        class="text-green-600 hover:text-green-900" 
                                        aria-label="Modules" 
                                        title="Manage Modules">
                                            <i class="fas fa-layer-group"></i>
                                        </a>

                                        <!-- Submit for Review Button - Always Visible -->
                                        <form action="{{ route('instructor.courses.submit-review', $course) }}" 
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900" 
                                                    aria-label="Submit for Review" 
                                                    title="Submit for Review"
                                                    onclick="return confirm('Are you sure you want to submit this course for review?')">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>

                                        <!-- Withdraw from Review Button - Always Visible -->
                                        @if($course->isPendingReview())
                                        <form action="{{ route('instructor.courses.withdraw-review', $course) }}" 
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-orange-600 hover:text-orange-900" 
                                                    aria-label="Withdraw from Review" 
                                                    title="Withdraw from Review"
                                                    onclick="return confirm('Are you sure you want to withdraw this course from review?')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Delete Button - Always Visible -->
                                        <form action="{{ route('instructor.courses.destroy', $course) }}" 
                                            method="POST" 
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    aria-label="Delete" 
                                                    title="Delete Course"
                                                    onclick="return confirmDelete()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                                    No courses found. <a href="{{ route('instructor.courses.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first course</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (sm:hidden) -->
            <div class="sm:hidden px-4 space-y-4 mt-6">
                @foreach ($courses as $course)
                    <div class="border border-gray-200 rounded-lg p-4 flex space-x-4 items-start shadow-sm bg-white">
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
                            <div class="text-base font-semibold text-gray-900 truncate">{{ $course->title }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ Str::limit($course->description, 50) }}</div>

                            <div class="mt-2 flex items-center justify-between text-sm text-gray-600">
                                <div>
                                    @if($course->is_premium)
                                        ${{ number_format($course->price, 2) }}
                                    @else
                                        <span class="text-green-600 font-medium">Free</span>
                                    @endif
                                </div>
                                <div>
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending_review' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'published' => 'bg-blue-100 text-blue-800',
                                            'rejected' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Draft',
                                            'pending_review' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'published' => 'Published',
                                            'rejected' => 'Rejected'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$course->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$course->status] ?? $course->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 flex space-x-3">
                                <a href="{{ route('instructor.courses.show', $course) }}" 
                                class="text-indigo-600 hover:text-indigo-900" 
                                title="View Course">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('instructor.courses.analytics', $course) }}" 
                                class="text-purple-600 hover:text-purple-900" 
                                title="View Analytics">
                                    <i class="fas fa-chart-bar"></i>
                                </a>

                                <a href="{{ route('instructor.courses.edit', $course) }}" 
                                class="text-blue-600 hover:text-blue-900" 
                                title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('instructor.courses.modules', $course) }}" 
                                class="text-green-600 hover:text-green-900" 
                                title="Manage Modules">
                                    <i class="fas fa-layer-group"></i>
                                </a>

                                <form action="{{ route('instructor.courses.submit-review', $course) }}" 
                                    method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            class="text-yellow-600 hover:text-yellow-900" 
                                            title="Submit for Review"
                                            onclick="return confirm('Submit for review?')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>

                                <form action="{{ route('instructor.courses.destroy', $course) }}" 
                                    method="POST" 
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Delete Course"
                                            onclick="return confirmDelete()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

function showMessage(message) {
    alert(message);
    return false;
}

// Add smooth messaging for form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Check for flash messages and show them smoothly
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Remove toast after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endsection