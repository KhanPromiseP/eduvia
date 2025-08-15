<div class="ad-image-container bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden" 
     style="width: 300px; height: 250px; position: relative;">
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
           class="block w-full h-full group" data-clickable>
            <img src="{{ $ad->content }}" 
                 alt="{{ $ad->title }}" 
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                 loading="lazy" />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
        </a>
    @else
        <img src="{{ $ad->content }}" 
             alt="{{ $ad->title }}" 
             class="w-full h-full object-cover"
             loading="lazy" />
    @endif
    <div class="ad-label">Ad</div>
</div>