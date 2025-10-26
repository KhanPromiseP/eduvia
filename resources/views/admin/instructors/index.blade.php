@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Instructors Management</h1>
            <p class="text-gray-600 mt-1">Manage all approved instructors on the platform</p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="text-sm text-gray-500">
                Total Instructors: <span class="font-semibold text-gray-700">{{ $instructors->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $instructors->total() }}</h3>
                    <p class="text-sm text-gray-500">Total Instructors</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ \App\Models\Course::whereIn('instructor_id', $instructors->pluck('id'))->count() }}

                    </h3>
                    <p class="text-sm text-gray-500">Total Courses</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border-l-4 border-purple-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ \App\Models\Instructor::sum('total_students') }}
                    </h3>
                    <p class="text-sm text-gray-500">Total Students</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ number_format(\App\Models\Instructor::avg('rating') ?? 0, 1) }}/5.0
                    </h3>
                    <p class="text-sm text-gray-500">Average Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructors Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-lg font-semibold text-gray-900">All Instructors</h2>
                
                <!-- Search and Filter -->
                <div class="mt-2 md:mt-0 flex space-x-2">
                    <input type="text" placeholder="Search instructors..." 
                           class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <select class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4+ Stars</option>
                        <option value="3">3+ Stars</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Instructor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expertise
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Courses
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Students
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rating
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($instructors as $instructor)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($instructor->user->profile_path)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $instructor->user->profile_path) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($instructor->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $instructor->user->name }}
                                        @if($instructor->is_verified)
                                            <i class="fas fa-check-circle text-blue-500 ml-1" title="Verified Instructor"></i>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $instructor->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $instructor->headline ?? 'No headline' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($instructor->skills && count($instructor->skills) > 0)
                                    {{ implode(', ', array_slice($instructor->skills, 0, 2)) }}{{ count($instructor->skills) > 2 ? '...' : '' }}
                                @else
                                    No skills listed
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $instructor->user->courses->count() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ number_format($instructor->total_students) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($instructor->rating))
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @elseif($i - 0.5 <= $instructor->rating)
                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm text-gray-500">
                                    ({{ $instructor->total_reviews }})
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($instructor->is_verified) bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $instructor->is_verified ? 'Approved' : 'Pending' }}
                            </span>
                       
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($instructor->isSuspended()) bg-red-100 text-red-800
                                @elseif($instructor->isActive()) bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                @if($instructor->isSuspended()) Suspended
                                @elseif($instructor->isActive()) Active
                                @else Pending @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.instructors.show', $instructor) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm transition">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                
                                <a href="#" 
                                   class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm transition">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                
                               @if($instructor)
                                    @if(!$instructor->isSuspended())
                                <button onclick="openSuspendModal({{ $instructor->id }})"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm transition">
                                        <i class="fas fa-ban mr-1"></i>
                                        Suspend
                                    </button>
                                    @else
                                    <form action="{{ route('admin.instructors.reactivate', $instructor) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm transition">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Reactivate
                                        </button>
                                    </form>
                                    <span class="text-gray-500 bg-gray-100 px-3 py-1 rounded-md text-sm" title="Suspended on {{ $instructor->suspended_at->format('M j, Y') }}">
                                        <i class="fas fa-ban mr-1"></i>
                                        Suspended
                                    </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No instructors found</p>
                            <p class="text-sm mt-1">Approved instructors will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($instructors->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $instructors->links() }}
        </div>
        @endif
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
                        Are you sure you want to suspend this instructor? They will lose access to instructor privileges.
                    </p>
                </div>
                <div class="mt-4">
                    <form id="suspendForm" method="POST">
    @csrf
    <div class="mb-4">
        <label for="suspend_reason" class="block text-sm font-medium text-gray-700 mb-1">
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
.hover\:bg-gray-50:hover {
    transition: all 0.2s ease;
}
</style>
@endpush