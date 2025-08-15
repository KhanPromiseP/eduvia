<div class="ad-banner-container bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg shadow-lg border border-gray-200 overflow-hidden" 
     style="width: 728px; height: 90px; position: relative;">
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
           class="block w-full h-full hover:bg-gradient-to-r hover:from-blue-100 hover:to-purple-100 transition-all duration-300" data-clickable>
            <div class="p-4 h-full flex items-center justify-center">
                {!! $ad->content !!}
            </div>
        </a>
    @else
        <div class="p-4 h-full flex items-center justify-center">
            {!! $ad->content !!}
        </div>
    @endif
    <div class="ad-label">Ad</div>
</div>