<div class="ad-video-container bg-black rounded-lg shadow-lg overflow-hidden" 
     style="width: 400px; height: 225px; position: relative;">
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
           class="absolute inset-0 z-10" data-clickable>
            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300">
                <span class="text-white font-semibold">Visit Advertiser</span>
            </div>
        </a>
    @endif
    <video 
        class="w-full h-full object-cover" 
        autoplay 
        muted 
        loop
        playsinline
        poster=""
        onloadstart="this.muted=true">
        <source src="{{ $ad->content }}" type="video/mp4">
        <source src="{{ $ad->content }}" type="video/webm">
        Your browser does not support the video tag.
    </video>
    <div class="ad-label">Ad</div>
</div>