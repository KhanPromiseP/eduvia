<div class="border border-gray-200 rounded-xl p-6 hover:border-gray-300 transition duration-200 review-item" data-review-id="{{ $review->id }}">
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center space-x-3">
            @if($review->user->profile_path)
                <img src="{{ asset('storage/' . $review->user->profile_path) }}" 
                     alt="{{ $review->user->name }}" 
                     class="w-10 h-10 rounded-full">
            @else
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-indigo-600 font-semibold text-sm">
                        {{ substr($review->user->name, 0, 1) }}
                    </span>
                </div>
            @endif
            <div>
                <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-sm"></i>
                            @endif
                        @endfor
                    </div>
                    <span>•</span>
                    <span>{{ $review->created_at->format('M j, Y') }}</span>
                    @if($review->is_verified)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full flex items-center">
                            <i class="fas fa-check mr-1 text-xs"></i> Verified Student
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @auth
            @if(auth()->id() === $review->user_id || auth()->user()->is_admin)
                <div class="flex space-x-2 review-actions">
                    @if(auth()->id() === $review->user_id)
                        <button onclick="editReview({{ $review->id }})" 
                                class="text-gray-400 hover:text-indigo-600 transition duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                    @endif
                    <button onclick="confirmDelete({{ $review->id }})" 
                            class="text-gray-400 hover:text-red-600 transition duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endif
        @endauth
    </div>

    <p class="text-gray-700 leading-relaxed review-comment">{{ $review->comment }}</p>

    <!-- Helpful Votes -->
    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center space-x-4">
            <button class="flex items-center space-x-1 text-gray-500 hover:text-gray-700 transition duration-200 helpful-btn"
                    data-review-id="{{ $review->id }}">
                <i class="far fa-thumbs-up"></i>
                <span>Helpful ({{ $review->helpful_votes ?? 0 }})</span>
            </button>
        </div>
        
        @if(auth()->id() === $review->user_id)
            <span class="text-sm text-indigo-600 font-medium">Your Review</span>
        @endif
    </div>
</div>