<div class="ad-image-container bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden group relative"
     style="
        width: {{ 
            $placement === 'sidebar' ? '160px' : 
            ($placement === 'header' ? '728px' : 
            ($placement === 'footer' ? '728px' : 
            ($placement === 'in-content' ? '468px' : 
            ($placement === 'floating' ? '300px' : 
            ($placement === 'popup' ? '400px' : 
            ($placement === 'interstitial' ? '600px' : '250px')))))) 
        }};
        height: {{ 
            $placement === 'sidebar' ? '300px' : 
            ($placement === 'header' ? '100px' : 
            ($placement === 'footer' ? '90px' : 
            ($placement === 'in-content' ? '250px' : 
            ($placement === 'floating' ? '250px' : 
            ($placement === 'popup' ? '400px' : 
            ($placement === 'interstitial' ? '500px' : '250px')))))) 
        }};
        {{ $placement === 'sidebar' ? 'position:fixed; top:50%; transform:translateY(-50%); z-index:50;' : 'position:relative;' }}
        {{ $placement === 'sidebar' ? ($side === 'left' ? 'left:10px;' : 'right:10px;') : '' }}
        {{ $placement === 'floating' ? 'position:fixed; bottom:20px; right:20px; z-index:60;' : '' }}
        {{ $placement === 'popup' ? 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:70;' : '' }}
        {{ $placement === 'interstitial' ? 'position:fixed; top:0; left:0; right:0; bottom:0; margin:auto; z-index:80; background:white;' : '' }}
     ">

    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer nofollow" 
           class="block w-full h-full relative overflow-hidden" data-clickable @click="trackAdClick">
            
            <!-- Main Image with Smooth Zoom -->
            <img src="{{ $ad->content }}" 
                 alt="{{ $ad->title }}" 
                 class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110"
                 loading="lazy" />
            
            <!-- Gradient Overlay for Text Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <!-- Title & CTA Section -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
               <h3 class="inline-block bg-black text-white font-bold text-xl px-2 py-1 rounded">
                    {{ $ad->title }}
               </h3>

                
                <style>
  /* Custom animations */
  @keyframes glow {
    0% { box-shadow: 0 0 10px #3b82f6, 0 0 20px #9333ea; }
    50% { box-shadow: 0 0 20px #9333ea, 0 0 40px #3b82f6; }
    100% { box-shadow: 0 0 10px #3b82f6, 0 0 20px #9333ea; }
  }

  @keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    50% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
  }

  .glow-animate {
    animation: glow 2s infinite alternate;
  }

  .hand-wave {
    display: inline-block;
    margin-left: 8px;
    font-size: 20px;
    animation: wiggle 1.2s infinite ease-in-out;
  }
</style>

<div class="relative flex justify-center mt-10">
  <!-- CTA Button -->
  <button class="cta-button bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-full text-lg font-bold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 flex items-center">
    <span>Click Now</span>
    <svg class="w-5 h-5 ml-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
    </svg>
  </button>

  <!-- Animated Hand (outside the button, pointing to it) -->
  <div class="absolute -bottom-1 right-[63%] rotate-60 animate-bounce">
    👉
  </div>
</div>


            </div>

            <!-- Floating Stars -->
            <div class="floating-stars absolute inset-0 pointer-events-none overflow-hidden">
                <div class="star star-1">★</div>
                <div class="star star-2">✧</div>
                <div class="star star-3">✦</div>
                <div class="star star-4">✶</div>
                <div class="star star-5">✷</div>
                <div class="star star-6">✸</div>
            </div>
        </a>
    @else
        <img src="{{ $ad->content }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy" />
    @endif

    <!-- Ad Label -->
    <div class="ad-label absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full flex items-center">
        <span>Ad</span>
        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
</div>

<style>
    /* === Continuous Floating Stars === */
.floating-stars {
    opacity: 0;
    transition: opacity 0.5s ease;
}
.group:hover .floating-stars {
    opacity: 1;
}

.star {
    position: absolute;
    color: rgba(255, 215, 0, 0.8);
    font-size: 1.2rem;
    animation: floatUp linear infinite;
    opacity: 0;
}

/* Keyframes for floating upwards with fade in/out */
@keyframes floatUp {
    0%   { transform: translateY(0) scale(0.8) rotate(0deg); opacity: 0; }
    10%  { opacity: 1; }
    50%  { opacity: 1; }
    100% { transform: translateY(-120px) scale(1.2) rotate(360deg); opacity: 0; }
}

/* Different star positions + animation delays for variety */
.star-1 { top: 90%; left: 20%; animation-duration: 4s; animation-delay: 0s; }
.star-2 { top: 85%; left: 70%; animation-duration: 5s; animation-delay: 1s; }
.star-3 { top: 80%; left: 40%; animation-duration: 6s; animation-delay: 2s; }
.star-4 { top: 75%; left: 60%; animation-duration: 7s; animation-delay: 3s; }
.star-5 { top: 88%; left: 10%; animation-duration: 5s; animation-delay: 1.5s; }
.star-6 { top: 82%; left: 50%; animation-duration: 6.5s; animation-delay: 2.5s; }

</style>
