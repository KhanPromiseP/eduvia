@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Manage Modules: {{ $course->title }}</h1>
            <p class="text-gray-600">Add and organize course modules and content</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
            Back to Courses
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Module Form -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Module</h2>
                
                <form action="{{ route('admin.courses.modules.store', $course) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Module Title *</label>
                        <input type="text" name="title" id="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                        <input type="number" name="order" id="order" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_free" id="is_free" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_free" class="ml-2 block text-sm text-gray-900">Free Preview Module</label>
                        </div>
                        @error('is_free')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                        Add Module
                    </button>
                </form>
            </div>
            
            <!-- Quick Stats -->
            <div class="bg-white shadow-md rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Modules:</span>
                        <span class="font-medium">{{ $course->modules->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Free Modules:</span>
                        <span class="font-medium">{{ $course->modules->where('is_free', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Attachments:</span>
                        <span class="font-medium">{{ $course->modules->sum(fn($module) => $module->attachments->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modules List -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Course Modules</h2>
                
                @if($course->modules->count() > 0)
                <div class="space-y-4">
                    @foreach($course->modules->sortBy('order') as $module)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold">{{ $module->title }}</h3>
                                <p class="text-sm text-gray-600">Order: {{ $module->order }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($module->is_free)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Free Preview</span>
                                @endif
                                <button onclick="toggleModuleEdit({{ $module->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.courses.modules.destroy', [$course, $module]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Delete this module?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Edit Module Form (Hidden by default) -->
                        <div id="edit-module-{{ $module->id }}" class="hidden p-4 bg-gray-50 border-t">
                            <form action="{{ route('admin.courses.modules.update', [$course, $module]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                        <input type="text" name="title" value="{{ $module->title }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                        <input type="number" name="order" value="{{ $module->order }}" min="0" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ $module->description }}</textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_free" id="is_free_{{ $module->id }}" value="1"
                                               {{ $module->is_free ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="is_free_{{ $module->id }}" class="ml-2 block text-sm text-gray-900">Free Preview</label>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="toggleModuleEdit({{ $module->id }})" 
                                            class="bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Attachments Section -->
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-700">Attachments</h4>
                                <button onclick="toggleAttachmentForm({{ $module->id }})" 
                                        class="text-sm bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                                    <i class="fas fa-plus mr-1"></i> Add Attachment
                                </button>
                            </div>
                            
                            <!-- Add Attachment Form (Hidden by default) -->
                            <div id="attachment-form-{{ $module->id }}" class="hidden mb-4 p-3 bg-gray-50 rounded">
                                <form action="{{ route('admin.courses.attachments.store', [$course, $module]) }}" 
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                            <input type="text" name="title" required
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                            <input type="number" name="order" min="0" required
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="2"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                placeholder="Optional description for this attachment"></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                        <input type="file" name="file"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            accept=".pdf,.doc,.docx,.mp4,.mov,.avi,.jpg,.jpeg,.png,.zip">
                                        <p class="text-xs text-gray-500 mt-1">OR enter a video URL below</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                        <input type="url" name="video_url"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            placeholder="https://youtube.com/... or https://vimeo.com/...">
                                        <p class="text-xs text-gray-500 mt-1">Supports YouTube and Vimeo links</p>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="toggleAttachmentForm({{ $module->id }})" 
                                                class="text-sm bg-gray-300 text-gray-700 px-2 py-1 rounded">
                                            Cancel
                                        </button>
                                        <button type="submit" class="text-sm bg-indigo-600 text-white px-2 py-1 rounded">
                                            Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Attachments List -->
                            @if($module->attachments->count() > 0)
                            <div class="space-y-2">
                                @foreach($module->attachments->sortBy('order') as $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                    <div class="flex items-center">
                                        @if($attachment->file_type === 'pdf')
                                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                        @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi']))
                                            <i class="fas fa-video text-purple-600 mr-2"></i>
                                        @elseif($attachment->file_type === 'external_video')
                                            <i class="fab fa-youtube text-red-600 mr-2"></i>
                                        @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png']))
                                            <i class="fas fa-image text-green-600 mr-2"></i>
                                        @else
                                            <i class="fas fa-file text-gray-600 mr-2"></i>
                                        @endif
                                        
                                        <div>
                                            <span class="text-sm font-medium">{{ $attachment->title }}</span>
                                            @if($attachment->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($attachment->description, 50) }}</p>
                                            @endif
                                            <span class="text-xs text-gray-500">
                                                @if($attachment->file_type === 'external_video')
                                                    External Video
                                                @else
                                                    {{ strtoupper($attachment->file_type) }}
                                                    @if($attachment->file_size)
                                                        ({{ number_format($attachment->file_size / 1024, 1) }} KB)
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($attachment->file_type === 'external_video')
                                            <a href="{{ $attachment->video_url }}" 
                                            target="_blank" class="text-blue-600 hover:text-blue-900 text-sm" title="View Video">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                            target="_blank" class="text-blue-600 hover:text-blue-900 text-sm" title="View File">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        
                                        <!-- Edit Attachment Button -->
                                        <button onclick="toggleAttachmentEdit({{ $attachment->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900 text-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.courses.attachments.destroy', [$course, $module, $attachment]) }}" 
                                            method="POST" onsubmit="return confirm('Delete this attachment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Edit Attachment Form (Hidden by default) -->
                                <div id="edit-attachment-{{ $attachment->id }}" class="hidden p-3 bg-gray-100 rounded mt-2">
                                    <form action="{{ route('admin.courses.attachments.update', [$course, $module, $attachment]) }}" 
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                                <input type="text" name="title" value="{{ $attachment->title }}" required
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                                <input type="number" name="order" value="{{ $attachment->order }}" min="0" required
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" rows="2"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm">{{ $attachment->description }}</textarea>
                                        </div>
                                        
                                        @if($attachment->file_type !== 'external_video')
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Replace File</label>
                                            <input type="file" name="file"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                accept=".pdf,.doc,.docx,.mp4,.mov,.avi,.jpg,.jpeg,.png,.zip">
                                        </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                            <input type="url" name="video_url" value="{{ $attachment->video_url }}"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                placeholder="https://youtube.com/...">
                                        </div>
                                        
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="toggleAttachmentEdit({{ $attachment->id }})" 
                                                    class="text-sm bg-gray-300 text-gray-700 px-2 py-1 rounded">
                                                Cancel
                                            </button>
                                            <button type="submit" class="text-sm bg-indigo-600 text-white px-2 py-1 rounded">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500 italic">No attachments yet.</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No modules added yet. Add your first module to get started.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleModuleEdit(moduleId) {
    const editForm = document.getElementById('edit-module-' + moduleId);
    editForm.classList.toggle('hidden');
}

function toggleAttachmentEdit(attachmentId) {
    const editForm = document.getElementById('edit-attachment-' + attachmentId);
    editForm.classList.toggle('hidden');
}

function toggleAttachmentForm(moduleId) {
    const attachmentForm = document.getElementById('attachment-form-' + moduleId);
    attachmentForm.classList.toggle('hidden');
}
</script>
@endsection