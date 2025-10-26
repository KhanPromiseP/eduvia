@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                @if($application->status === 'pending')
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Application Under Review</h1>
                    <p class="text-gray-600">We're currently reviewing your instructor application</p>
                @elseif($application->status === 'approved')
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Application Approved!</h1>
                    <p class="text-gray-600">Congratulations! You're now an instructor</p>
                @else
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Application Not Approved</h1>
                    <p class="text-gray-600">We appreciate your interest in becoming an instructor</p>
                @endif
            </div>

            <!-- Status Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Application Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Submitted:</span>
                                <span class="font-medium">{{ $application->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium capitalize">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($application->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $application->status }}
                                    </span>
                                </span>
                            </div>
                            @if($application->reviewed_by)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reviewed by:</span>
                                    <span class="font-medium">{{ $application->reviewer->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reviewed on:</span>
                                    <span class="font-medium">{{ $application->updated_at->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($application->review_notes)
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Review Notes</h3>
                        <p class="text-sm text-gray-600 bg-white p-3 rounded border">{{ $application->review_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($application->status === 'pending')
                <!-- Document Verification Status -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Document Verification Status</h2>
                    
                    @php
                        $instructor = \App\Models\Instructor::where('user_id', $application->user_id)->first();
                        $documents = $instructor ? $instructor->documents : collect();
                    @endphp
                    
                    @if($documents->count() > 0)
                    <div class="space-y-4">
                        @foreach($documents as $document)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas 
                                    @if($document->isApproved()) fa-check-circle text-green-500
                                    @elseif($document->isRejected()) fa-times-circle text-red-500
                                    @else fa-clock text-yellow-500 @endif
                                    mr-3">
                                </i>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $document->document_type_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($document->isApproved()) Verified
                                        @elseif($document->isRejected()) Rejected
                                        @else Under Review @endif
                                    </p>
                                </div>
                            </div>
                            @if($document->isRejected())
                            <span class="text-sm text-red-600 bg-red-50 px-2 py-1 rounded">
                                Needs re-upload
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">Documents are being processed...</p>
                    @endif
                </div>
                @endif

            <!-- Next Steps -->
            @if($application->status === 'pending')
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-800 mb-3">What's Next?</h3>
                <ul class="space-y-2 text-sm text-blue-700">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle mr-2 text-blue-500"></i>
                        Your application is in our review queue
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                        Typical review time: 2-3 business days
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>
                        You'll receive an email notification once reviewed
                    </li>
                </ul>
            </div>
            @elseif($application->status === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-green-800 mb-3">Welcome to the Instructor Team!</h3>
                <div class="space-y-3 text-sm text-green-700">
                    <p>You now have access to the instructor dashboard where you can:</p>
                    <ul class="space-y-1 ml-4">
                        <li>• Create and manage your courses</li>
                        <li>• Track your student enrollments</li>
                        <li>• Monitor your earnings</li>
                        <li>• Update your instructor profile</li>
                    </ul>
                </div>
            </div>
            @else
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-red-800 mb-3">Future Opportunities</h3>
                <p class="text-sm text-red-700 mb-3">
                    While your current application wasn't approved, we encourage you to:
                </p>
                <ul class="space-y-1 text-sm text-red-700 ml-4">
                    <li>• Gain more experience in your field</li>
                    <li>• Consider reapplying in the future</li>
                    <li>• Explore other ways to contribute to our community</li>
                </ul>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($application->status === 'approved')
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Instructor Dashboard
                    </a>
                @elseif($application->status === 'rejected')
                    <a href="{{ route('instructor.welcome') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-redo mr-2"></i>
                        Learn More About Requirements
                    </a>
                @endif
                
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-home mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection