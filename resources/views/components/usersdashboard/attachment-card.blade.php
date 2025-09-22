<div class="border rounded-lg p-4 hover:shadow-md transition group secure-content">
    <div class="flex items-start">
        <!-- File Icon -->
        <div class="mr-4 flex-shrink-0">
            @if($attachment->isExternalVideo())
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fab fa-youtube text-red-600 text-xl"></i>
                </div>
            @elseif($attachment->file_type === 'pdf')
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-video text-purple-600 text-xl"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-green-600 text-xl"></i>
                </div>
            @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-word text-blue-600 text-xl"></i>
                </div>
            @else
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file text-gray-600 text-xl"></i>
                </div>
            @endif
        </div>
        
        <!-- File Info -->
        <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-800 truncate">{{ $attachment->title }}</h4>
            @if($attachment->description)
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($attachment->description, 60) }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">
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
    <div class="mt-4">
        <button onclick="openResourceViewer('{{ $attachment->id }}', '{{ $attachment->file_type }}', '{{ $attachment->title }}', '{{ $attachment->isExternalVideo() ? $attachment->video_url : asset('storage/' . $attachment->file_path) }}', '{{ $attachment->isExternalVideo() ? 'external_video' : 'file' }}')" 
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center group-hover:bg-indigo-700">
            <i class="fas fa-eye mr-2"></i> View Resource
        </button>
    </div>
</div>