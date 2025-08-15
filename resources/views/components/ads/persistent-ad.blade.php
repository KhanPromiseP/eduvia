<div class="ad-persistent-container bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-xl border-t border-indigo-500" 
     style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; min-height: 60px;">
    <div class="container mx-auto px-4 py-3">
        @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
               class="flex items-center justify-between hover:bg-white hover:bg-opacity-10 rounded-lg px-4 py-2 transition-all duration-300" data-clickable>
                <div class="flex-1">
                    <h4 class="font-bold text-lg">{{ $ad->title }}</h4>
                    <div class="text-sm opacity-90">
                        {!! $ad->content !!}
                    </div>
                </div>
                <div class="ml-4">
                    <span class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg font-semibold transition-all duration-300">
                        Get Started →
                    </span>
                </div>
            </a>
        @else
            <div class="flex items-center justify-between px-4 py-2">
                <div class="flex-1">
                    <h4 class="font-bold text-lg">{{ $ad->title }}</h4>
                    <div class="text-sm opacity-90">
                        {!! $ad->content !!}
                    </div>
                </div>
            </div>
        @endif
        <div class="ad-label" style="color: #333;">Ad</div>
    </div>
</div>