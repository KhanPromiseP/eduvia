<div class="flex items-center justify-between p-4 border rounded hover:bg-gray-50 transition secure-content">
    <div class="flex items-center flex-1 min-w-0">
        <!-- File Icon -->
        <div class="flex-shrink-0 mr-4">
            @if($attachment->isExternalVideo())
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fab fa-youtube text-red-600"></i>
                </div>
            @elseif($attachment->file_type === 'pdf')
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-red-600"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-purple-600"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-green-600"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-word text-blue-600"></i>
                </div>
            @else
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file text-gray-600"></i>
                </div>
            @endif
        </div>
        
        <!-- File Info -->
        <div class="flex-1 min-w-0 mr-4">
            <h4 class="font-semibold text-gray-800 truncate">{{ $attachment->title }}</h4>
            @if($attachment->description)
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($attachment->description, 80) }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">
                {{ $attachment->getDisplayFileType() }} • 
                @if($attachment->file_size)
                    {{ number_format($attachment->file_size / 1024, 1) }} KB
                @elseif($attachment->isExternalVideo())
                    External Video
                @else
                    Size unknown
                @endif
            </p>
        </div>
    </div>
    
    <!-- Action Button -->
    <button onclick="openResourceViewer('{{ $attachment->id }}', '{{ $attachment->file_type }}', '{{ $attachment->title }}', '{{ $attachment->isExternalVideo() ? $attachment->video_url : asset('storage/' . $attachment->file_path) }}', '{{ $attachment->isExternalVideo() ? 'external_video' : 'file' }}')" 
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center text-sm">
        <i class="fas fa-eye mr-2"></i> View
    </button>
</div>