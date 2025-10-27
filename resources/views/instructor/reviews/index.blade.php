@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">My Reviews</h1>
        <div class="flex items-center space-x-4">
            <div class="bg-white rounded-lg shadow-sm px-4 py-2">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Average Rating:</span>
                    <span class="font-semibold text-indigo-600">{{ number_format($averageRating, 1) }} ⭐</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalReviews }}</p>
                </div>
                <i class="fas fa-comments text-blue-500 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">5-Star Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $fiveStarReviews }}</p>
                </div>
                <i class="fas fa-star text-green-500 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Response Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $responseRate }}%</p>
                </div>
                <i class="fas fa-reply text-yellow-500 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentReviews }}</p>
                </div>
                <i class="fas fa-chart-line text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rating Distribution</h3>
        <div class="space-y-3">
            @for($i = 5; $i >= 1; $i--)
            <div class="flex items-center">
                <div class="w-16 flex items-center space-x-2">
                    <span class="text-sm text-gray-600">{{ $i }} star</span>
                    <span class="text-xs text-gray-400">({{ $ratingDistribution[$i] ?? 0 }})</span>
                </div>
                <div class="flex-1 mx-3">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-400 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $totalReviews > 0 ? (($ratingDistribution[$i] ?? 0) / $totalReviews) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="w-16 text-sm text-gray-600 text-right">
                    {{ $totalReviews > 0 ? number_format((($ratingDistribution[$i] ?? 0) / $totalReviews) * 100, 1) : 0 }}%
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">All Reviews</h3>
                <div class="flex space-x-2">
                    <select id="filterRating" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select id="filterCourse" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">All Courses</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ Str::limit($course->title, 30) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200" id="reviewsList">
            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                <div class="p-6 review-item hover:bg-gray-50 transition-colors duration-200" 
                     data-rating="{{ $review->rating }}" 
                     data-course="{{ $review->course_id }}">
                    
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <!-- User Avatar with Safe Check -->
                            <img src="{{ $review->user && $review->user->profile_path ? asset('storage/' . $review->user->profile_path) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $review->user->name ?? 'User' }}" 
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200">
                            <div>
                                <!-- Safe User Data -->
                                <h4 class="font-semibold text-gray-900">{{ $review->user->name ?? 'Unknown User' }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $review->course->title ?? 'Unknown Course' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-{{ $i <= $review->rating ? 'yellow-400' : 'gray-300' }} text-sm"></i>
                                @endfor
                                <span class="ml-1 text-sm font-medium text-gray-700">{{ $review->rating }}.0</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $review->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Review Comment -->
                    <div class="mb-4">
                        <p class="text-gray-700 leading-relaxed">{{ $review->comment ?? 'No comment provided.' }}</p>
                    </div>
                    
                    <!-- Instructor Response (if exists) -->
                    @if($review->instructor_response ?? false)
                    <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-reply text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-semibold text-blue-900 text-sm">Your Response</span>
                                    @if($review->response_date)
                                    <span class="text-xs text-blue-600">{{ $review->response_date->format('M j, Y') }}</span>
                                    @endif
                                </div>
                                <p class="text-blue-800 text-sm leading-relaxed">{{ $review->instructor_response }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-3">
                            @if(!($review->instructor_response ?? false))
                            <button class="reply-btn text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center transition-colors duration-200"
                                    data-review-id="{{ $review->id }}"
                                    data-review-rating="{{ $review->rating }}"
                                    data-review-comment="{{ $review->comment }}">
                                <i class="fas fa-reply mr-1"></i> Reply
                            </button>
                            @endif
                            <button class="report-btn text-gray-500 hover:text-red-600 text-sm font-medium inline-flex items-center transition-colors duration-200"
                                    data-review-id="{{ $review->id }}">
                                <i class="fas fa-flag mr-1"></i> Report
                            </button>
                        </div>
                        
                        <!-- Review Status Badges -->
                        <div class="flex space-x-2">
                            @if($review->is_verified ?? false)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Verified
                            </span>
                            @endif
                            @if($review->is_helpful ?? false)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-thumbs-up mr-1"></i> Helpful
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Reply Form (Hidden by default) -->
                    <div id="replyForm-{{ $review->id }}" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                        <form action="{{ route('instructor.reviews.reply', $review) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                                <textarea name="response" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                          placeholder="Write your professional response to this review..."
                                          required></textarea>
                                <p class="text-xs text-gray-500 mt-1">Your response will be visible to all students.</p>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                        class="cancel-reply-btn px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors duration-200"
                                        data-review-id="{{ $review->id }}">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors duration-200">
                                    Submit Response
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            @else
                <!-- No Reviews State -->
                <div class="text-center py-12">
                    <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Reviews Yet</h3>
                    <p class="text-gray-500 mb-6">You haven't received any reviews for your courses yet.</p>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-600">Tips to get more reviews:</p>
                        <ul class="text-sm text-gray-500 space-y-1 max-w-md mx-auto">
                            <li class="flex items-center justify-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Encourage students to leave reviews after course completion
                            </li>
                            <li class="flex items-center justify-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Provide high-quality course content
                            </li>
                            <li class="flex items-center justify-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Engage with students in discussions
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($reviews->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
                </div>
                <div class="flex space-x-2">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Report Review</h3>
        </div>
        <div class="px-6 py-4">
            <form id="reportForm">
                @csrf
                <input type="hidden" name="review_id" id="reportReviewId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for reporting</label>
                    <select name="reason" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select a reason</option>
                        <option value="spam">Spam or misleading</option>
                        <option value="inappropriate">Inappropriate content</option>
                        <option value="harassment">Harassment or bullying</option>
                        <option value="false_information">False information</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional details</label>
                    <textarea name="details" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please provide more details about why you're reporting this review..."
                              ></textarea>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" 
                    class="cancel-report-btn px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors duration-200">
                Cancel
            </button>
            <button type="button" 
                    class="submit-report-btn px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors duration-200">
                Submit Report
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.review-item {
    transition: all 0.2s ease;
}

.review-item:hover {
    background-color: #f9fafb;
}

.star-rating {
    display: inline-flex;
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Smooth transitions for all interactive elements */
button, select, textarea {
    transition: all 0.2s ease;
}
</style>
@endpush


<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reviews page loaded');

    // Filter functionality
    const filterRating = document.getElementById('filterRating');
    const filterCourse = document.getElementById('filterCourse');
    const reviewItems = document.querySelectorAll('.review-item');

    function filterReviews() {
        const ratingValue = filterRating.value;
        const courseValue = filterCourse.value;

        console.log('Filtering reviews:', { ratingValue, courseValue });

        let visibleCount = 0;

        reviewItems.forEach(item => {
            const itemRating = item.getAttribute('data-rating');
            const itemCourse = item.getAttribute('data-course');

            const ratingMatch = ratingValue === 'all' || itemRating === ratingValue;
            const courseMatch = courseValue === 'all' || itemCourse === courseValue;

            if (ratingMatch && courseMatch) {
                item.style.display = 'block';
                visibleCount++;
                item.classList.add('fade-in');
            } else {
                item.style.display = 'none';
                item.classList.remove('fade-in');
            }
        });

        console.log('Visible reviews:', visibleCount);

        // Show no results message if needed
        const noResultsElement = document.getElementById('noResults');
        if (visibleCount === 0 && reviewItems.length > 0) {
            if (!noResultsElement) {
                const noResults = document.createElement('div');
                noResults.id = 'noResults';
                noResults.className = 'text-center py-12';
                noResults.innerHTML = `
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No matching reviews</h3>
                    <p class="text-gray-500">Try adjusting your filters to see more results.</p>
                `;
                document.getElementById('reviewsList').appendChild(noResults);
            }
        } else if (noResultsElement) {
            noResultsElement.remove();
        }
    }

    if (filterRating) filterRating.addEventListener('change', filterReviews);
    if (filterCourse) filterCourse.addEventListener('change', filterReviews);

    // Reply functionality
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const replyForm = document.getElementById(`replyForm-${reviewId}`);
            
            if (replyForm) {
                replyForm.classList.toggle('hidden');
                if (!replyForm.classList.contains('hidden')) {
                    replyForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        });
    });

    document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const replyForm = document.getElementById(`replyForm-${reviewId}`);
            if (replyForm) {
                replyForm.classList.add('hidden');
            }
        });
    });

    // Report functionality
    const reportModal = document.getElementById('reportModal');
    let currentReviewId = null;

    document.querySelectorAll('.report-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentReviewId = this.getAttribute('data-review-id');
            document.getElementById('reportReviewId').value = currentReviewId;
            if (reportModal) {
                reportModal.classList.remove('hidden');
            }
        });
    });

    document.querySelector('.cancel-report-btn')?.addEventListener('click', function() {
        if (reportModal) {
            reportModal.classList.add('hidden');
            document.getElementById('reportForm').reset();
        }
    });

    document.querySelector('.submit-report-btn')?.addEventListener('click', function() {
        const form = document.getElementById('reportForm');
        if (!form) return;

        const formData = new FormData(form);

        fetch('{{ route("instructor.reviews.report") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (reportModal) reportModal.classList.add('hidden');
                form.reset();
                showNotification('Report submitted successfully', 'success');
            } else {
                showNotification('Error submitting report: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error submitting report', 'error');
        });
    });

    // Enhanced notification system
    function showNotification(message, type) {
        const alertClass = type === 'success' 
            ? 'bg-green-100 border-green-400 text-green-700' 
            : 'bg-red-100 border-red-400 text-red-700';
        
        const icon = type === 'success' 
            ? '<i class="fas fa-check-circle mr-2"></i>' 
            : '<i class="fas fa-exclamation-circle mr-2"></i>';
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg border ${alertClass} font-medium z-50 flex items-center shadow-lg transform transition-transform duration-300 translate-x-full`;
        notification.innerHTML = `${icon}${message}`;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Remove after delay
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    // Initialize filters on page load
    setTimeout(filterReviews, 100);
});
</script>
