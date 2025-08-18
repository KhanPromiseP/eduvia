<div class="ad-image-container bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden group relative"
     style="width: 250px; height: 250px;">
     
    @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer nofollow" 
           class="block w-full h-full relative overflow-hidden" data-clickable>
            
            <!-- Main Image with Smooth Zoom -->
            <img src="{{ $ad->content }}" 
                 alt="{{ $ad->title }}" 
                 class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110"
                 loading="lazy" />
            
            <!-- Gradient Overlay for Text Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <!-- Title & CTA Section -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <!-- Animated Title (Slides Up) -->
                <h3 class="text-white font-bold text-xl mb-2 transform translate-y-5 group-hover:translate-y-0 transition-all duration-500">
                    {{ $ad->title }}
                </h3>
                
                <!-- Pulsating CTA Button (Always Visible) -->
                <button class="cta-button bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2.5 rounded-full text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 flex items-center mx-auto">
                    <span>Click Now</span>
                    <svg class="w-4 h-4 ml-2 animate-bounce-fast" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Continuous Floating Stars -->
            <div class="floating-stars absolute inset-0 pointer-events-none overflow-hidden">
                <!-- Multiple stars with different animations -->
                <div class="star star-1">★</div>
                <div class="star star-2">✧</div>
                <div class="star star-3">✦</div>
                <div class="star star-4">✶</div>
                <div class="star star-5">✷</div>
                <div class="star star-6">✸</div>
            </div>
        </a>
    @else
        <img src="{{ $ad->content }}" 
             alt="{{ $ad->title }}" 
             class="w-full h-full object-cover"
             loading="lazy" />
    @endif
    
    <!-- Ad Label (Top Right) -->
    <div class="ad-label absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full flex items-center">
        <span>Ad</span>
        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
</div>

<style>
    /* === CTA Button Animations === */
    .cta-button {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); box-shadow: 0 0 15px rgba(59, 130, 246, 0.6); }
    }
    .animate-bounce-fast {
        animation: bounce-fast 1s infinite;
    }
    @keyframes bounce-fast {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(px); }
    }

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
        animation: float 3s infinite ease-in-out;
    }
    @keyframes float {
        0% { transform: translateY(0) rotate(0deg); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
    }
    .star-1 { top: 80%; left: 20%; animation-delay: 0s; }
    .star-2 { top: 70%; left: 80%; animation-delay: 1s; }
    .star-3 { top: 60%; left: 30%; animation-delay: 2s; }
    .star-4 { top: 50%; left: 70%; animation-delay: 3s; }
    .star-5 { top: 40%; left: 10%; animation-delay: 4s; }
    .star-6 { top: 30%; left: 60%; animation-delay: 5s; }

    /* === Container Hover Effects === */
    .ad-image-container {
        
        transition: transform 0.9s ease, box-shadow 0.7s ease;
    }
    .ad-image-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
</style>