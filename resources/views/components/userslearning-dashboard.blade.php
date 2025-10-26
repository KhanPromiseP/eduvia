<div class="mx-auto px-2 sm:px-4 lg:px-6 py-4">

    <!-- Navigation Breadcrumbs -->
    @include('components.usersdashboard.breadcrumbs')
    
    <!-- Welcome Header -->
    @include('components.usersdashboard.welcome-header')
    
    <!-- Main Content Area -->
    <div class="flex flex-col lg:flex-row gap-2">

        {{-- @include('components.usersdashboard.margin-tools') --}}
        {{-- @include('components.margin-dock') --}}

        <!-- Course List Sidebar -->
        <div class="lg:w-1/4 xl:w-1/5">
            @include('components.usersdashboard.course-sidebar')
        </div>
        
        <!-- Learning Content Area -->
        <div class="lg:w-3/4 xl:w-4/5">
            @if(isset($selectedCourse) && isset($selectedModule))
                @include('components.usersdashboard.module-interface')
            @elseif(isset($selectedCourse))
                @include('components.usersdashboard.course-overview')
            @else
                @include('components.usersdashboard.default-view')
            @endif
        </div>
    </div>
</div>

<!-- Resource Viewer -->
@include('components.usersdashboard.resource-viewer')