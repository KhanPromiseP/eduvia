@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Instructor Management Center</h1>

      <!-- Welcome Message for New Instructors -->
   @if($stats['total_courses'] == 0 && $stats['total_students'] == 0 && $stats['total_revenue'] == 0)
    <div id="newInstructorWelcome" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-lg mb-8 animate-fade-in">
        <div class="p-8 relative">
            <!-- Dismiss Button -->
            <button onclick="dismissWelcome()" class="absolute top-4 right-4 text-orange-500 hover:text-orange-700 transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
   
            <div class="flex flex-col md:flex-row items-center">
                <!-- Icon Section -->
                <div class="flex-shrink-0 mb-6 md:mb-0 md:mr-8">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-3">
                        Welcome to Your Instructor Journey! 🎉
                    </h2>
                    
                    <div class="space-y-3 mb-6">
                        <p class="text-lg text-gray-700 leading-relaxed">
                            <strong>Congratulations!</strong> Your instructor application has been approved and we're thrilled to have you join our community of exceptional educators.
                        </p>
                        
                        <p class="text-gray-600 leading-relaxed">
                            You're now part of a platform dedicated to transforming lives through quality education. 
                            Your expertise will inspire and empower learners around the world.
                        </p>
                        
                        <div class="bg-white rounded-lg p-4 border border-indigo-100 mt-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                <span><strong>Pro Tip:</strong> Start with one comprehensive course to build your reputation and gather valuable student feedback.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ route('instructor.courses.create') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Create Your First Course
                        </a>
                        
                        <a href="{{ route('instructor.documentation') ?? '#' }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-indigo-300 text-base font-medium rounded-lg text-indigo-700 bg-white hover:bg-indigo-50 shadow-md transition-all duration-200">
                            <i class="fas fa-book-open mr-2"></i>
                            View Instructor Guide
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Preview -->
            <div class="mt-8 pt-6 border-t border-indigo-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="text-2xl font-bold text-indigo-600 mb-1">70%</div>
                        <div class="text-sm text-gray-600">Your Revenue Share</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="text-2xl font-bold text-indigo-600 mb-1">Global</div>
                        <div class="text-sm text-gray-600">Student Reach</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="text-2xl font-bold text-indigo-600 mb-1">24/7</div>
                        <div class="text-sm text-gray-600">Support Available</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   

<script>
function dismissWelcome() {
    const welcomeMessage = document.getElementById('newInstructorWelcome');
    if (welcomeMessage) {
        welcomeMessage.style.opacity = '0';
        welcomeMessage.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            welcomeMessage.remove();
        }, 300);
        
        // Optional: Store in localStorage so it doesn't show again
        localStorage.setItem('instructorWelcomeDismissed', 'true');
    }
}

// Check if user has previously dismissed the message
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('instructorWelcomeDismissed') === 'true') {
        const welcomeMessage = document.getElementById('newInstructorWelcome');
        if (welcomeMessage) {
            welcomeMessage.remove();
        }
    }
});
</script>
@endif

    <!-- Instructor Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- My Courses Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Courses</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_courses'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Published: {{ $stats['published_courses'] }}</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Students Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-indigo-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Students</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_students'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Unique students enrolled</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Enrollments Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Enrollments</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_enrollments'] }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">Course enrollments</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Revenue Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Revenue</dt>
                            <dd class="text-lg font-semibold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</dd>
                            <dt class="text-xs text-gray-500 mt-1">From my courses</dt>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Instructor Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Course Performance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Performance</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Rating</span>
                    <span class="text-lg font-semibold text-indigo-600">
                        {{ number_format($stats['average_rating'], 1) }} ⭐
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Student Engagement</span>
                    <span class="text-lg font-semibold text-green-600">
                        {{ $stats['total_students'] > 0 ? number_format(($stats['total_enrollments'] / $stats['total_students']), 1) : 0 }}x
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('instructor.courses.create') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Create New Course
                </a>
                <a href="{{ route('instructor.courses.index') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-book mr-2"></i> Manage Courses
                </a>
                <a href="{{ route('instructor.students') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-users mr-2"></i> View Students
                </a>
            </div>
        </div>
    </div>

   <!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
        <div class="flex space-x-2" id="activityTabs">
            <button class="activity-tab px-3 py-1 text-sm font-medium rounded-lg transition-all duration-200 bg-indigo-100 text-indigo-700 border border-indigo-200" data-tab="enrollments">
                <i class="fas fa-user-plus mr-1"></i> Enrollments
            </button>
            <button class="activity-tab px-3 py-1 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 border border-transparent" data-tab="payments">
                <i class="fas fa-dollar-sign mr-1"></i> Payments
            </button>
            <button class="activity-tab px-3 py-1 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 border border-transparent" data-tab="reviews">
                <i class="fas fa-star mr-1"></i> Reviews
            </button>
        </div>
    </div>

    <!-- Enrollments Tab -->
    <div id="enrollmentsTab" class="activity-content space-y-3">
        @if($stats['recent_enrollments']->count() > 0)
            @foreach($stats['recent_enrollments'] as $enrollment)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='{{ route('instructor.students.detail', $enrollment->user_id) }}'">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $enrollment->user->name ?? 'Student' }}</p>
                        <p class="text-xs text-gray-500">Enrolled in {{ Str::limit($enrollment->course->title, 25) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">{{ $enrollment->purchased_at->diffForHumans() }}</p>
                    <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full mt-1">New</span>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-6">
                <i class="fas fa-user-plus text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No recent enrollments</p>
                <p class="text-sm text-gray-400 mt-1">Student enrollments will appear here</p>
            </div>
        @endif
        
        @if($stats['recent_enrollments']->count() > 0)
        <div class="text-center pt-3">
            <a href="{{ route('instructor.students') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center">
                View All Students
                <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        @endif
    </div>

    <!-- Payments Tab -->
    <div id="paymentsTab" class="activity-content hidden space-y-3">
        @if($stats['recent_payments']->count() > 0)
            @foreach($stats['recent_payments'] as $payment)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $payment->user->name ?? 'Student' }}</p>
                        <p class="text-xs text-gray-500">Purchased {{ Str::limit($payment->userCourse->course->title ?? 'Course', 20) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-green-600 text-sm">${{ number_format($payment->amount, 2) }}</p>
                    <p class="text-xs text-gray-500">{{ $payment->completed_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-6">
                <i class="fas fa-dollar-sign text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No recent payments</p>
                <p class="text-sm text-gray-400 mt-1">Payment activity will appear here</p>
            </div>
        @endif
        
        @if($stats['recent_payments']->count() > 0)
        <div class="text-center pt-3">
            <a href="{{ route('instructor.earnings') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center">
                View Earnings Details
                <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        @endif
    </div>

    <!-- Reviews Tab -->
    <!-- Reviews Tab -->
<div id="reviewsTab" class="activity-content hidden space-y-3">
    @if($stats['recent_reviews']->count() > 0)
        @foreach($stats['recent_reviews'] as $review)
        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" 
             onclick="window.location='{{ route('instructor.reviews') }}'">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-xs"></i>
                    </div>
                    <span class="font-medium text-gray-900 text-sm">{{ $review->user->name ?? 'Student' }}</span>
                </div>
                <div class="flex items-center space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-{{ $i <= $review->rating ? 'yellow-400' : 'gray-300' }} text-xs"></i>
                    @endfor
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-2 line-clamp-2">{{ $review->review ?? $review->comment }}</p>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">{{ $review->course->title ?? 'Course' }}</span>
                <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
    @else
        <div class="text-center py-6">
            <i class="fas fa-star text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No recent reviews</p>
            <p class="text-sm text-gray-400 mt-1">Student reviews will appear here</p>
        </div>
    @endif
    
    @if($stats['recent_reviews']->count() > 0)
    <div class="text-center pt-3">
        <a href="{{ route('instructor.reviews') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center">
            Manage All Reviews
            <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    @endif
</div>
</div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
/* Custom styles for better mobile experience */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .text-lg {
        font-size: 1.125rem;
    }
    
    .text-2xl {
        font-size: 1.5rem;
    }
}

/* Card hover effects */
.bg-white {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endpush

@push('scripts')

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.activity-tab {
    transition: all 0.2s ease;
}

.activity-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Smooth hover effects */
.bg-gray-50 {
    transition: all 0.2s ease;
}

.bg-gray-50:hover {
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .text-lg {
        font-size: 1.125rem;
    }
    
    .text-2xl {
        font-size: 1.5rem;
    }
}

/* Card hover effects */
.bg-white {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Welcome message animation */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

/* Gradient text for emphasis */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabs = document.querySelectorAll('.activity-tab');
    const contents = document.querySelectorAll('.activity-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => {
                t.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
                t.classList.add('text-gray-600', 'hover:bg-gray-100', 'border-transparent');
            });
            this.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-200');
            this.classList.remove('text-gray-600', 'hover:bg-gray-100', 'border-transparent');
            
            // Show target content
            contents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(targetTab + 'Tab').classList.remove('hidden');
        });
    });

    // Auto-refresh activity every 30 seconds
    setInterval(() => {
        // can implement AJAX refresh here if needed
        console.log('Activity auto-refresh triggered');
    }, 30000);
});
</script>
