<header x-data="{ open: false }" class="sticky top-0 bg-white shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        
        <!-- Logo -->
       <div class="flex items-center space-x-3">
            
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <a href="/">
            <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
            </a>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center space-x-8">
            {{-- <a href="{{ route('blog.index') }}" class="text-black px-5 py-2 rounded-full font-medium hover:text-blue-500">Blogs</a>
            <a href="{{ route('service.index') }}" class="text-black px-5 py-2 rounded-full font-medium hover:text-blue-500">Services</a>
            <a href="{{ route('contact.index') }}" class="text-black px-5 py-2 rounded-full font-medium hover:text-blue-500">Contact</a> --}}

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 shadow-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 shadow-sm">Get Started</a>
                    @endif
                @endauth
            @endif
        </nav>

        <!-- Mobile Hamburger -->
        <button @click="open = true" class="lg:hidden p-2 rounded-md text-gray-700 hover:text-indigo-600 focus:outline-none">
            <!-- Hamburger Icon -->
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Off-Canvas Mobile Menu -->
    <div 
        x-show="open" 
        x-transition:enter="transition transform duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-0 z-40 flex lg:hidden"
    >
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>

        <!-- Side Menu -->
        <div class="relative bg-white w-64 max-w-xs h-full shadow-xl overflow-y-auto">
            <!-- Close Button -->
            <button @click="open = false" class="absolute top-3 right-3 text-gray-700 font-bold text-xl p-1 hover:text-red-600">
                ✕
            </button>

            <!-- Menu Content -->
            <div class="pt-6 pb-4 px-6 space-y-4">

                {{-- <x-responsive-nav-link :href="route('blog.index')">Blog</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('contact.index')">Contact</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('service.index')">Services</x-responsive-nav-link> --}}

                @auth
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                      
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-responsive-nav-link>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <x-responsive-nav-link :href="route('login')">Login</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
