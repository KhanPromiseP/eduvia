<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <a href="{{ route('dashboard') }}">
                    <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
                </a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden sm:flex sm:space-x-8 sm:ml-10 items-center">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Home</x-nav-link>
                <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">Courses</x-nav-link>
                <x-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.*')">Blog</x-nav-link>
                <x-nav-link :href="route('contact.index')" :active="request()->routeIs('contact.*')">Contact</x-nav-link>
                <x-nav-link :href="route('service.index')" :active="request()->routeIs('service.*')">Services</x-nav-link>

                @auth
                <x-nav-link :href="route('userdashboard')" :active="request()->routeIs('userdashboard')">MyDashboard</x-nav-link>
                @endauth
                

                @auth
                <!-- User Profile / Dropdown only for desktop (authenticated users) -->
                <x-dropdown align="right" class="ml-4">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="h-10 w-10 rounded-full object-cover border border-gray-300">
                            @else
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold border border-gray-300">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>

                        <x-dropdown-link :href="route('userdashboard')">MyDashboard</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        @if(Auth::user()->is_admin)
                            <x-dropdown-link :href="route('admin.dashboard')">Admin Dashboard</x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <!-- Login and Start buttons for unauthenticated users -->
                <div class="flex items-center space-x-4 ml-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors px-3 py-2 rounded-md text-sm font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors text-sm font-medium">
                        Get Started
                    </a>
                </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="sm:hidden flex items-center">
                @auth
                <!-- Hamburger menu for authenticated users -->
                <button @click="open = true" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @else
                <!-- Login and Start buttons for unauthenticated users on mobile -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors px-2 py-1 rounded-md text-sm font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md transition-colors text-sm font-medium">
                        Start
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak class="sm:hidden">

    @auth
    <!-- Off-Canvas Mobile Menu (only for authenticated users) -->
    <div x-show="open" 
         x-transition:enter="transition transform duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition transform duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         @click.away="open = false"
         class="fixed inset-0 z-40 flex">

        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>

        <!-- Side Menu -->
        <div class="relative bg-white w-64 max-w-xs h-full shadow-xl overflow-y-auto">
            <!-- Close Button -->
            <button @click="open = false" class="absolute top-3 right-3 text-gray-700 font-bold text-xl p-1 hover:text-red-600">✕</button>

            <!-- User Profile Info -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="h-10 w-10 rounded-full object-cover border border-gray-300">
                    @else
                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold border border-gray-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Mobile Links & Profile -->
            <div class="py-2 px-4 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Home</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">Courses</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.*')">Blog</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('contact.index')" :active="request()->routeIs('contact.*')">Contact</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('service.index')" :active="request()->routeIs('service.*')">Services</x-responsive-nav-link>

                <!-- Profile Links (replaces desktop dropdown on mobile) -->
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('userdashboard')">MyDashboard</x-responsive-nav-link>

                @if(Auth::user()->is_admin)
                    <x-responsive-nav-link :href="route('admin.dashboard')">Admin Dashboard</x-responsive-nav-link>
                @endif

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    </div>
</nav>