<div class="ad-overlay" id="popup-overlay-{{ $adId }}">
    <div class="ad-popup-container bg-white rounded-xl shadow-2xl border border-gray-300 overflow-hidden max-w-md mx-4" 
         style="position: relative; animation: adPopupBounce 0.5s ease-out;">
        @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
               class="block hover:bg-gray-50 transition-colors duration-200" data-clickable>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $ad->title }}</h3>
                    <div class="text-gray-600">
                        {!! $ad->content !!}
                    </div>
                    <div class="mt-4 text-right">
                        <span class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                            Learn More
                        </span>
                    </div>
                </div>
            </a>
        @else
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $ad->title }}</h3>
                <div class="text-gray-600">
                    {!! $ad->content !!}
                </div>
            </div>
        @endif
        <div class="ad-label">Ad</div>
    </div>
</div>

<style>
@keyframes adPopupBounce {
    0% { 
        transform: scale(0.3) rotate(10deg); 
        opacity: 0; 
    }
    50% { 
        transform: scale(1.05) rotate(-2deg); 
        opacity: 0.8; 
    }
    100% { 
        transform: scale(1) rotate(0deg); 
        opacity: 1; 
    }
}
</style>