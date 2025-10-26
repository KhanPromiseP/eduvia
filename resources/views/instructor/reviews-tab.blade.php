@if($reviews->count() > 0)
    <div class="space-y-6">
        @foreach($reviews as $review)
        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
            <div class="flex items-start space-x-4">
                <img src="{{ $review->user->profile_photo_path ? asset('storage/' . $review->user->profile_photo_path) : asset('images/default-avatar.png') }}" 
                     alt="{{ $review->user->name }}" 
                     class="w-12 h-12 rounded-full object-cover">
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                            @if($review->course)
                                <p class="text-sm text-gray-600">Reviewed: {{ $review->course->title }}</p>
                            @endif
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
                    <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Pagination -->
        @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
@else
    <div class="text-center text-gray-500 py-12">
        <i class="fas fa-comments text-6xl mb-4 text-gray-300"></i>
        <h4 class="text-lg font-semibold text-gray-600 mb-2">No Reviews Yet</h4>
        <p class="text-gray-500">This instructor doesn't have any reviews yet.</p>
        @auth
            @if(auth()->id() !== $instructor->user_id)
            <button onclick="openReviewModal()" 
                    class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                Be the first to review
            </button>
            @endif
        @endauth
    </div>
@endif