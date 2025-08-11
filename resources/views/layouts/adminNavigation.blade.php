<nav class="bg-white border-b border-gray-100 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto flex justify-between h-16 items-center">
        
        <!-- Left Navigation -->
        <div class="flex items-center space-x-8">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>
        </div>

        <!-- Right: User Dropdown -->
        <x-dropdown align="right">
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
                <!-- User Info -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <!-- Links -->
                <x-dropdown-link :href="route('dashboard')">
                    <i class="bi bi-speedometer2 mr-2"></i> {{ __('Dashboard') }}
                </x-dropdown-link>

                <x-dropdown-link :href="route('profile.edit')">
                    <i class="bi bi-person-circle mr-2"></i> {{ __('Profile') }}
                </x-dropdown-link>

                @if(Auth::user()->is_admin)
                    <x-dropdown-link :href="route('admin.admin.dashboard')">
                        <i class="bi bi-speedometer2 mr-2"></i> {{ __('Admin Dashboard') }}
                    </x-dropdown-link>
                @endif

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="bi bi-box-arrow-right mr-2"></i> {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>
