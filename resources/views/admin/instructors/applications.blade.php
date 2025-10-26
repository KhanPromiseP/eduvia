@extends('layouts.admin')

@section('content')
{{-- Add this at the top of your content section in both views --}}
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <div>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
        <div>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Instructor Applications</h1>
            <p class="text-gray-600 mt-1">Review and manage instructor applications</p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="flex items-center space-x-4 text-sm">
                <span class="flex items-center">
                    <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                    Pending: {{ $pendingCount }}
                </span>
                <span class="flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    Approved: {{ $approvedCount }}
                </span>
                <span class="flex items-center">
                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                    Rejected: {{ $rejectedCount }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $pendingCount }}</h3>
                    <p class="text-sm text-gray-500">Pending Applications</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $approvedCount }}</h3>
                    <p class="text-sm text-gray-500">Approved Applications</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border-l-4 border-red-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $rejectedCount }}</h3>
                    <p class="text-sm text-gray-500">Rejected Applications</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-lg font-semibold text-gray-900">All Applications</h2>
                
                <!-- Status Filter -->
                <div class="mt-2 md:mt-0">
                    <select id="statusFilter" class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
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
                            Applicant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expertise
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Submitted
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reviewer
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <tr class="hover:bg-gray-50 transition application-row" data-status="{{ $application->status }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($application->user->profile_path)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $application->user->profile_path) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($application->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $application->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $application->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->expertise }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $application->created_at->format('M j, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $application->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($application->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($application->status) }}
                                
                            </span>
                            @php
                                    $instructor = \App\Models\Instructor::where('user_id', $application->user_id)->first();
                                @endphp

                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($instructor->isSuspended()) bg-red-100 text-red-800
                                @elseif($instructor->isActive()) bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                @if($instructor->isSuspended()) Suspended
                                @elseif($instructor->isActive()) Active
                                @else Pending @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($application->reviewer)
                                {{ $application->reviewer->name }}
                            @else
                                <span class="text-gray-400">Not reviewed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.instructors.applications.show', $application) }}" 
                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm transition">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                
                                @if($application->status === 'pending')
                                <button onclick="openApproveModal({{ $application->id }})"
                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm transition">
                                    <i class="fas fa-check mr-1"></i>
                                    Approve
                                </button>
                                <button onclick="openRejectModal({{ $application->id }})"
                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm transition">
                                    <i class="fas fa-times mr-1"></i>
                                    Reject
                                </button>
                                @elseif($application->status === 'approved')
                                @php
                                    $instructor = \App\Models\Instructor::where('user_id', $application->user_id)->first();
                                @endphp
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
                            @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No applications found</p>
                            <p class="text-sm mt-1">When users apply to become instructors, their applications will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applications->links() }}
        </div>
        @endif
    </div>
</div>



<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Approve Application</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to approve this instructor application? The user will be granted instructor privileges.
                    </p>
                </div>
                <div class="mt-4">
                    <form id="approveForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-1">Review Notes (Optional)</label>
                            <textarea name="review_notes" id="approve_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" placeholder="Add any notes for the applicant..."></textarea>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Approve Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Reject Application</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to reject this instructor application?
                    </p>
                </div>
                <div class="mt-4">
                    <form id="rejectForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-1">Review Notes *</label>
                            <textarea name="review_notes" id="reject_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" placeholder="Please provide reasons for rejection..." required></textarea>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Reject Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                <i class="fas fa-ban text-orange-600 text-xl"></i>
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
                            <label for="suspend_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Suspension *</label>
                            <textarea name="reason" id="suspend_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500" placeholder="Provide reason for suspension..." required></textarea>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition">
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
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.application-row');
            
            rows.forEach(row => {
                if (!status || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// Modal functions
function openApproveModal(applicationId) {
    console.log('Opening approve modal for application:', applicationId);
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    if (form && modal) {
        form.action = `/admin/instructors/applications/${applicationId}/approve`;
        modal.classList.remove('hidden');
    }
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function openRejectModal(applicationId) {
    console.log('Opening reject modal for application:', applicationId);
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    if (form && modal) {
        form.action = `/admin/instructors/applications/${applicationId}/reject`;
        modal.classList.remove('hidden');
    }
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

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

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    const suspendModal = document.getElementById('suspendModal');
    
    if (approveModal) {
        window.addEventListener('click', function(event) {
            if (event.target === approveModal) {
                closeApproveModal();
            }
        });
    }
    
    if (rejectModal) {
        window.addEventListener('click', function(event) {
            if (event.target === rejectModal) {
                closeRejectModal();
            }
        });
    }
    
    if (suspendModal) {
        window.addEventListener('click', function(event) {
            if (event.target === suspendModal) {
                closeSuspendModal();
            }
        });
    }
});
</script>
@endpush