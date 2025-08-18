{{-- resources/views/components/video-ad.blade.php --}}
@props(['ad'])

@php
    // Default ad settings
    $type = $ad->type ?? 'inline'; // inline | sidebar
    $placement = $ad->placement ?? 'bottom'; // left | right | bottom
@endphp

<div 
    class="ad-video-container bg-black rounded-lg shadow-lg overflow-hidden relative group"
    style="width: {{ $type === 'sidebar' ? '160px' : '400px' }}; 
           height: {{ $type === 'sidebar' ? '300px' : '225px' }}; 
           {{ $type === 'sidebar' ? 'position:fixed;' : 'position:relative;' }}
           {{ $type === 'sidebar' ? ($placement === 'left' ? 'left: 10px;' : 'right: 10px;') : '' }}
           {{ $type === 'sidebar' ? 'top: 50%; transform: translateY(-50%); z-index: 50;' : '' }}"
>
    {{-- Clickable Overlay --}}
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
           class="absolute inset-0 z-10" data-clickable>
            <div class="absolute inset-0 flex items-center justify-center 
                        bg-black bg-opacity-50 opacity-0 
                        group-hover:opacity-100 transition-opacity duration-300">
                <span class="text-white font-semibold">Visit Advertiser</span>
            </div>
        </a>
    @endif

    {{-- Video --}}
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

    {{-- Ad Label --}}
    <div class="absolute top-1 left-1 bg-yellow-500 text-black text-xs font-bold px-2 py-1 rounded">
        Ad
    </div>
</div>
