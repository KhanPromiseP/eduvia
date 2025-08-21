<div 
  x-data="{ 
      open: true, 
      showCloseBtn: false, 
      progress: 0, 
      interval: null,
      dismissAfter: {{ $dismissAfter ?? 12000 }},
      closeDelay: {{ $closeDelay ?? 5000 }}
  }"
>
  <template x-if="open">
    <div
      x-init="
          setTimeout(() => showCloseBtn = true, closeDelay);
          interval = setInterval(() => {
              if (progress < 100) {
                  progress += (100 / (dismissAfter/100));
              } else {
                  clearInterval(interval);
                  open = false;
              }
          }, 100);
      "
      x-transition:enter="ease-out duration-500"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="ease-in duration-400"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    >
      <div 
        class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden transform transition-all"
        x-transition:enter="ease-out duration-500"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-400"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
      >
        <!-- Close Button -->
        <button 
          x-show="showCloseBtn"
          x-transition.opacity.duration.300ms
          @click="open = false" 
          class="absolute top-3 right-3 bg-gray-900 text-white w-9 h-9 rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition"
        >
          &times;
        </button>

        <!-- Ad Content -->
        @if($link ?? false)
            <a href="{{ $link }}" target="_blank" 
               class="block w-full h-full cursor-pointer" 
               @click="$dispatch('ad-clicked')">
                {!! $content !!}
            </a>
        @else
            {!! $content !!}
        @endif

        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 h-1 transition-all duration-100"
             :style="`width: ${progress}%; background-color: 
                      ${progress < 50 ? '#16a34a' : progress < 80 ? '#facc15' : '#dc2626'};`">
        </div>
      </div>
    </div>
  </template>
</div>
