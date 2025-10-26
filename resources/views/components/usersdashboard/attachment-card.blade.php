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
            
            <!-- Security Badge for Secure Videos -->
            @if($attachment->is_secure && $attachment->file_type === 'secure_video')
                <div class="flex items-center mt-2 space-x-2">
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center">
                        <i class="fas fa-shield-alt mr-1"></i> Secure
                    </span>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded flex items-center">
                        <i class="fas fa-lock mr-1"></i> Encrypted
                    </span>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="mt-4 space-y-2">
        <!-- In both attachment-card.blade.php and attachment-list-item.blade.php -->
@if($attachment->is_secure && $attachment->file_type === 'secure_video')
    <button data-attachment-action="view-secure-video"
            data-attachment-id="{{ $attachment->id }}"
            data-title="{{ $attachment->title }}"
            class="...">
        <i class="fas fa-shield-alt mr-2"></i> View Secure Video
    </button>
@elseif($attachment->isExternalVideo())
    <button data-attachment-action="external-video"
            data-video-url="{{ $attachment->video_url }}"
            data-title="{{ $attachment->title }}"
            class="...">
        <i class="fab fa-youtube mr-2"></i> Watch Video
    </button>
@else
    <button data-attachment-action="view"
            data-attachment-id="{{ $attachment->id }}"
            data-file-type="{{ $attachment->file_type }}"
            data-title="{{ $attachment->title }}"
            class="...">
        <i class="fas fa-eye mr-2"></i> View Resource
    </button>
@endif
        
        @if($attachment->allow_download && !$attachment->is_secure)
            <button data-attachment-action="download"
                    data-attachment-id="{{ $attachment->id }}"
                    data-title="{{ $attachment->title }}"
                    class="...">
                <i class="fas fa-download"></i>
            </button>
        @endif
    </div>
</div>

<!-- Backward Compatibility Script -->
<script>
// These functions will still work for backward compatibility
function openSecureVideo(attachmentId, videoId, title, description) {
    // Use the new unified system
    const event = new CustomEvent('attachment-action', {
        detail: {
            action: 'view-secure-video',
            attachmentId: attachmentId,
            title: title
        }
    });
    document.dispatchEvent(event);
}

function openAttachmentInDashboard(attachmentId, fileType, title, fileUrl, resourceType, description) {
    // Convert to new system based on resource type
    let action = 'view';
    if (resourceType === 'external_video') {
        action = 'external-video';
    } else if (fileType === 'secure_video') {
        action = 'view-secure-video';
    }
    
    const event = new CustomEvent('attachment-action', {
        detail: {
            action: action,
            attachmentId: attachmentId,
            fileType: fileType,
            title: title,
            fileUrl: fileUrl
        }
    });
    document.dispatchEvent(event);
}

// Make functions globally available
window.openSecureVideo = openSecureVideo;
window.openAttachmentInDashboard = openAttachmentInDashboard;
</script>