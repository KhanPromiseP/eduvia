<div class="mb-2">

    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('userdashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
            </li>
            @if(isset($selectedCourse))
            <li class="flex items-center">
                <span class="text-gray-400 mx-2">/</span>
                <a href="{{ route('userdashboard', ['course' => $selectedCourse->id]) }}" 
                   class="text-indigo-600 hover:text-indigo-800">
                    {{ Str::limit($selectedCourse->title, 25) }}
                </a>
            </li>
            @endif
            @if(isset($selectedModule))
            <li class="flex items-center">
                <span class="text-gray-400 mx-2">/</span>
                <span class="text-gray-600">{{ Str::limit($selectedModule->title, 25) }}</span>
            </li>
            @endif
        </ol>
    </nav>
</div>