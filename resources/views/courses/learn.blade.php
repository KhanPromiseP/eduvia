@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar with modules -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-4 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Course Modules</h3>
                
                <div class="space-y-2">
                    @foreach($course->modules as $module)
                    <a href="#module-{{ $module->id }}" 
                       class="block p-2 rounded hover:bg-gray-100 {{ $loop->first ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <div class="flex items-center">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs flex items-center justify-center mr-2">
                                {{ $loop->iteration }}
                            </span>
                            <span>{{ $module->title }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="lg:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $course->title }}</h1>
                <p class="text-gray-600">{{ $course->description }}</p>
            </div>
            
            @foreach($course->modules as $module)
            <div id="module-{{ $module->id }}" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">{{ $module->title }}</h2>
                
                @if($module->description)
                    <p class="text-gray-600 mb-6">{{ $module->description }}</p>
                @endif
                
                @if($module->attachments->count() > 0)
                <div class="space-y-4">
                    <h3 class="text-lg font-medium mb-3">Resources</h3>
                    
                    @foreach($module->attachments as $attachment)
                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <div class="flex items-center">
                            @if($attachment->file_type === 'pdf')
                                <i class="fas fa-file-pdf text-red-600 text-xl mr-3"></i>
                            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi']))
                                <i class="fas fa-video text-purple-600 text-xl mr-3"></i>
                            @else
                                <i class="fas fa-file text-gray-600 text-xl mr-3"></i>
                            @endif
                            <div>
                                <p class="font-medium">{{ $attachment->title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ strtoupper($attachment->file_type) }} • 
                                    @if($attachment->file_size)
                                        {{ number_format($attachment->file_size / 1024, 1) }} KB
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                           class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded text-sm hover:bg-indigo-200 transition"
                           download>
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection