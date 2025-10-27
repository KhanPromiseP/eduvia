@extends('layouts.admin')

@section('content')
{{-- Success/Error Messages --}}
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
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Application Details</h1>
            <p class="text-gray-600 mt-1">Review application from {{ $application->user->name }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.instructors.applications') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Applications
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Application Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Application Status</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($application->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Submitted:</span>
                        <p class="font-medium">{{ $application->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Last Updated:</span>
                        <p class="font-medium">{{ $application->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    @if($application->reviewed_by)
                    <div>
                        <span class="text-gray-500">Reviewed by:</span>
                        <p class="font-medium">{{ $application->reviewer->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Reviewed on:</span>
                        <p class="font-medium">{{ $application->updated_at->format('F j, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payout Information Section -->
            @if($instructor && $instructor->payouts)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-wallet text-green-600 mr-2"></i>
                    Payout Information
                </h2>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <p class="text-sm text-blue-700">
                            Payout setup completed on: {{ $instructor->payouts->created_at->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payout Method -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Payout Method</h4>
                        <div class="flex items-center">
                            @if($instructor->payouts->payout_method === 'mobile_money')
                                <i class="fas fa-mobile-alt text-blue-600 mr-2"></i>
                                <span class="text-gray-900">Mobile Money</span>
                            @elseif($instructor->payouts->payout_method === 'bank_account')
                                <i class="fas fa-university text-green-600 mr-2"></i>
                                <span class="text-gray-900">Bank Transfer</span>
                            @else
                                <i class="fas fa-wallet text-purple-600 mr-2"></i>
                                <span class="text-gray-900">Tranzak Wallet</span>
                            @endif
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Account Details</h4>
                        <div class="space-y-1">
                            <p class="text-gray-900">
                                <strong>Name:</strong> {{ $instructor->payouts->account_name }}
                            </p>
                            <p class="text-gray-900">
                                <strong>Number:</strong> {{ $instructor->payouts->account_number }}
                            </p>
                            @if($instructor->payouts->operator)
                            <p class="text-gray-900">
                                <strong>Operator/Bank:</strong> {{ $instructor->payouts->operator }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Currency & Settings -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Currency & Settings</h4>
                        <div class="space-y-1">
                            <p class="text-gray-900">
                                <strong>Currency:</strong> {{ $instructor->payouts->currency }}
                            </p>
                            <p class="text-gray-900">
                                <strong>Auto Payout:</strong> 
                                <span class="{{ $instructor->payouts->auto_payout ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $instructor->payouts->auto_payout ? 'Enabled' : 'Disabled' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Payout Threshold -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Payout Threshold</h4>
                        <div class="space-y-1">
                            <p class="text-gray-900">
                                <strong>Minimum Amount:</strong> 
                                {{ number_format($instructor->payouts->payout_threshold, 2) }} {{ $instructor->payouts->currency }}
                            </p>
                            <p class="text-xs text-gray-500">
                                @if($instructor->payouts->payout_threshold > 0)
                                    Automatic payouts when balance reaches this amount
                                @else
                                    No minimum threshold set
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Verification Status -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Account Verification:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            {{ $instructor->payouts->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $instructor->payouts->is_verified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>
            </div>
            @elseif($application->payout_setup_completed)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">Payout Setup Completed</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Applicant has completed payout setup, but payout information is not available.
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-gray-500 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Payout Setup Pending</h3>
                        <p class="text-sm text-gray-700 mt-1">
                            Applicant has not yet completed the payout setup process.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Applicant Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Applicant Information</h2>
                
                <div class="flex items-start space-x-4 mb-6">
                    <div class="flex-shrink-0">
                        @if($application->user->profile_path)
                            <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $application->user->profile_path) }}" alt="">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($application->user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $application->user->name }}</h3>
                        <p class="text-gray-600">{{ $application->user->email }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Member since {{ $application->user->created_at->format('F Y') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Expertise Area</h4>
                        <p class="text-gray-900">{{ $application->expertise }}</p>
                    </div>
                    
                    @if($instructor)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Professional Headline</h4>
                        <p class="text-gray-900">{{ $instructor->headline ?? 'Not provided' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bio & Professional Background -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Professional Bio</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $application->bio }}</p>
                </div>
            </div>

            <!-- Skills & Languages -->
            @if($instructor)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Skills & Languages</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">Skills & Technologies</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($instructor->skills ?? [] as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">Teaching Languages</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($instructor->languages ?? [] as $language)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    {{ $language }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Document Verification Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Document Verification</h2>
                
                @if($instructor && $instructor->documents->count() > 0)
                <div class="space-y-4">
                    @foreach($instructor->documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="font-medium text-gray-900">{{ $document->document_type_name }}</span>
                                <span class="ml-3 px-2 py-1 text-xs rounded-full 
                                    @if($document->isApproved()) bg-green-100 text-green-800
                                    @elseif($document->isRejected()) bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($document->status) }}
                                </span>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $document->file_path) }}" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="{{ asset('storage/' . $document->file_path) }}" 
                                   download 
                                   class="text-green-600 hover:text-green-800 text-sm">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                        
                        <!-- Document Actions -->
                        @if($document->isPending())
                        <div class="flex space-x-2 mt-2">
                            <form action="{{ route('admin.instructors.documents.approve', $document) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.instructors.documents.reject', $document) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                    Reject
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No documents uploaded yet.</p>
                @endif
            </div>

            <!-- Links & Additional Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
                
                <div class="space-y-3">
                    @if($application->linkedin_url)
                    <div class="flex items-center">
                        <i class="fab fa-linkedin text-blue-600 mr-3 w-5"></i>
                        <a href="{{ $application->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            LinkedIn Profile
                        </a>
                    </div>
                    @endif
                    
                    @if($application->website_url)
                    <div class="flex items-center">
                        <i class="fas fa-globe text-gray-600 mr-3 w-5"></i>
                        <a href="{{ $application->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Personal Website/Blog
                        </a>
                    </div>
                    @endif
                    
                    @if($application->video_intro)
                    <div class="flex items-center">
                        <i class="fab fa-youtube text-red-600 mr-3 w-5"></i>
                        <a href="{{ $application->video_intro }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Introduction Video
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Review Notes -->
            @if($application->review_notes)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Review Notes</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700">{{ $application->review_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar - Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Actions</h3>
                
                @if($application->status === 'pending')
                <div class="space-y-3">
                    <form action="{{ route('admin.instructors.applications.approve', $application) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="review_notes_approve" class="block text-sm font-medium text-gray-700 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea name="review_notes" id="review_notes_approve" rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 text-sm"
                                placeholder="Add notes for the applicant..."></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>
                            Approve Application
                        </button>
                    </form>

                    <form action="{{ route('admin.instructors.applications.reject', $application) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="review_notes_reject" class="block text-sm font-medium text-gray-700 mb-1">
                                Rejection Reason *
                            </label>
                            <textarea name="review_notes" id="review_notes_reject" rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-sm"
                                placeholder="Please provide reasons for rejection..." required></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>
                            Reject Application
                        </button>
                    </form>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-gray-500 mb-3">This application has been {{ $application->status }}.</p>
                    @if($application->status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>
                            Approved
                        </span>
                        <p class="text-sm text-gray-500 mt-2">
                            Approved on: {{ $application->updated_at->format('M j, Y') }}
                        </p>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                            <i class="fas fa-times mr-1"></i>
                            Rejected
                        </span>
                        <p class="text-sm text-gray-500 mt-2">
                            Rejected on: {{ $application->updated_at->format('M j, Y') }}
                        </p>
                    @endif
                </div>
                @endif
            </div>

            @if($application->status === 'approved' && $instructor)
                @if(!$instructor->isSuspended())
                <!-- Suspension Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6 border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Instructor Management</h3>
                    
                    <div class="space-y-3">
                        <button onclick="openSuspendModal({{ $instructor->id }})"
                           class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition">
                            <i class="fas fa-ban mr-2"></i>
                            Suspend Instructor
                        </button>
                        
                        <p class="text-xs text-gray-500 text-center">
                            Suspending will temporarily revoke instructor privileges.
                        </p>
                    </div>
                </div>
                @else
                <!-- Reactivation Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Instructor Management</h3>
                    
                    <div class="space-y-3">
                        <form action="{{ route('admin.instructors.reactivate', $instructor) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                                <i class="fas fa-check-circle mr-2"></i>
                                Reactivate Instructor
                            </button>
                        </form>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Suspended on: {{ $instructor->suspended_at->format('M j, Y \a\t g:i A') }}
                            </p>
                            @if($instructor->suspension_reason)
                            <p class="text-sm text-yellow-700 mt-1">
                                Reason: {{ $instructor->suspension_reason }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endif

            <!-- Applicant Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Applicant Stats</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Courses Purchased:</span>
                        <span class="font-medium">{{ $application->user->courses->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Account Age:</span>
                        <span class="font-medium">{{ $application->user->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Active:</span>
                        <span class="font-medium">{{ $application->user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- User Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Management</h3>
                
                <div class="space-y-2">
                    <a href="#" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </a>
                    
                    <a href="#" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-user-cog mr-2"></i>
                        View User Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Suspend Modal functions
function openSuspendModal(instructorId) {
    console.log('Opening suspend modal for instructor:', instructorId);
    const modal = document.getElementById('suspendModal');
    const form = document.getElementById('suspendForm');
    if (form) {
        form.action = `/admin/instructors/${instructorId}/suspend`;
    }
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeSuspendModal() {
    const modal = document.getElementById('suspendModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const suspendModal = document.getElementById('suspendModal');
    
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