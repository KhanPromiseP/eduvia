<div class="ad-js-container bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden" 
     style="min-width: 300px; min-height: 250px; position: relative;">
    <div id="js-ad-content-{{ $adId }}" class="w-full h-full"></div>
    <div class="ad-label">Ad</div>
    <script>
        (function() {
            try {
                const container = document.getElementById('js-ad-content-{{ $adId }}');
                {!! $ad->content !!}
            } catch (error) {
                console.error('Ad script error:', error);
                document.getElementById('js-ad-content-{{ $adId }}').innerHTML = 
                    '<div class="p-4 text-gray-500 text-center">Ad failed to load</div>';
            }
        })();
    </script>
</div>