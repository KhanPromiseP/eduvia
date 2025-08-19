{{-- resources/views/components/video-ad.blade.php --}}
@props(['ad'])

@php
    $placement = $ad->placement ?? 'inline';
    $side = $ad->side ?? 'right';
@endphp

<div 
    class="ad-video-container bg-black rounded-lg shadow-xl border border-gray-200 overflow-hidden group relative"
    style="
        width: {{ 
            $placement === 'sidebar' ? '190px' : 
            ($placement === 'header' ? '728px' : 
            ($placement === 'footer' ? '828px' : 
            ($placement === 'in-content' ? '468px' : 
            ($placement === 'floating' ? '300px' : 
            ($placement === 'popup' ? '400px' : 
            ($placement === 'interstitial' ? '600px' : '400px')))))) 
        }};
        height: {{ 
            $placement === 'sidebar' ? '300px' : 
            ($placement === 'header' ? '90px' : 
            ($placement === 'footer' ? '100px' : 
            ($placement === 'in-content' ? '250px' : 
            ($placement === 'floating' ? '250px' : 
            ($placement === 'popup' ? '400px' : 
            ($placement === 'interstitial' ? '500px' : '225px')))))) 
        }};
        {{ $placement === 'footer' ? 'position:absolute; left:50%; bottom:10px; transform:translateX(-50%); z-index:50;' : 'position:relative;' }}      
        {{ $placement === 'sidebar' ? 'position:fixed; top:40%; transform:translateY(-60%); z-index:50;' : 'position:relative;' }}
        {{ $placement === 'sidebar' ? ($side === 'left' ? 'left:10px;' : 'right:-60px;') : '' }}
        {{ $placement === 'floating' ? 'position:fixed; bottom:20px; right:20px; z-index:60;' : '' }}
        {{ $placement === 'popup' ? 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:70;' : '' }}
        {{ $placement === 'interstitial' ? 'position:fixed; top:0; left:0; right:0; bottom:0; margin:auto; z-index:80; background:white;' : '' }}
    "
>
    {{-- Video --}}
    <video 
        id="ad-video-{{ $ad->id ?? uniqid() }}"
        class="w-full h-full object-cover" 
        autoplay 
        muted 
        loop 
        playsinline
        poster=""
        onloadstart="this.muted=true"
    >
        <source src="{{ $ad->content }}" type="video/mp4">
        <source src="{{ $ad->content }}" type="video/webm">
        Your browser does not support the video tag.
    </video>

    {{-- Overlay for CTA --}}
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer nofollow fixed"
           class="absolute inset-0 z-10" data-clickable @click="trackAdClick">
            <div class="absolute bottom-12 left-0 right-0 p-4  rounded-lg
                        opacity-0 
                        group-hover:opacity-100 transition-opacity duration-300">
                <button class="cta-button bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2.5 rounded-full text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 flex items-center mx-auto">
                    <span>Click Here</span>
                    <svg class="w-4 h-4 ml-2 animate-bounce-fast" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </a>
    @endif


    {{-- Title Overlay --}}
    <h5 class="inline-block absolute bottom-1 left-1 right-12 bg-black/40 text-white text-ml px-2 py-1 rounded">
        {{ $ad->title }}
    </h5>


    {{-- Ad Label --}}
    <div class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full flex items-center">
        <span>Ad</span>
        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        
    </div>


     {{-- Close button --}}
   <button 
        class="close-btn absolute top-2 right-2 bg-black/60 hover:bg-black/80 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs z-20 opacity-0 pointer-events-none transition-opacity duration-500"
        onclick="this.closest('.ad-video-container').remove()">
        ✕
    </button>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                document.querySelectorAll('.ad-video-container .close-btn').forEach(btn => {
                    btn.classList.remove('opacity-0', 'pointer-events-none');
                    btn.classList.add('opacity-100');
                });
            }, 12000); // 11 seconds
        });
    </script>

    {{-- Volume Toggle --}}
    <button 
        class="absolute bottom-2 right-2 bg-black/60 hover:bg-black/80 text-white rounded-full w-8 h-8 flex items-center justify-center z-20"
        onclick="
            const vid = this.closest('.ad-video-container').querySelector('video'); 
            vid.muted = !vid.muted; 
            this.innerHTML = vid.muted ? '🔇' : '🔊';
        ">
        🔇
    </button>
</div>
