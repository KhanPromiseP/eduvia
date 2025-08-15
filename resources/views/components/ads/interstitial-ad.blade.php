<div class="ad-overlay" id="interstitial-overlay-{{ $adId }}">
    <div class="ad-interstitial-container bg-white rounded-2xl shadow-2xl overflow-hidden" 
         style="width: 90%; max-width: 600px; max-height: 80vh; position: relative;">
        @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
               class="block group" data-clickable>
                <div class="relative">
                    <div class="h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center">
                        <h2 class="text-3xl font-bold text-white text-center px-6">{{ $ad->title }}</h2>
                    </div>
                    <div class="p-8">
                        <div class="text-gray-700 text-lg leading-relaxed mb-6">
                            {!! $ad->content !!}
                        </div>
                        <div class="text-center">
                            <span class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform group-hover:scale-105 shadow-lg">
                                Discover More
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @else
            <div class="relative">
                <div class="h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center">
                    <h2 class="text-3xl font-bold text-white text-center px-6">{{ $ad->title }}</h2>
                </div>
                <div class="p-8">
                    <div class="text-gray-700 text-lg leading-relaxed">
                        {!! $ad->content !!}
                    </div>
                </div>
            </div>
        @endif
        <div class="ad-label">Ad</div>
    </div>
</div>