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
    
    <!-- Action Buttons -->
    <div class="flex items-center space-x-2">
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
// Backward compatibility for existing onclick handlers
function openAttachmentInDashboard(attachmentId, fileType, title, fileUrl, resourceType, description) {
    // Convert to new system based on resource type
    let action = 'view';
    if (resourceType === 'external_video') {
        action = 'external-video';
    } else if (fileType === 'secure_video') {
        action = 'view-secure-video';
    }
    
    // Trigger the new attachment handling system
    const button = document.createElement('button');
    button.setAttribute('data-attachment-action', action);
    button.setAttribute('data-attachment-id', attachmentId);
    button.setAttribute('data-file-type', fileType);
    button.setAttribute('data-title', title);
    if (resourceType === 'external_video') {
        button.setAttribute('data-video-url', fileUrl);
    }
    
    // Simulate click on the new system
    document.dispatchEvent(new CustomEvent('attachment-click', {
        detail: {
            target: button
        }
    }));
}

// Make function globally available
window.openAttachmentInDashboard = openAttachmentInDashboard;
</script>