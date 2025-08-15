<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        .overlay-transition {
            transition: opacity 0.3s ease-in-out;
        }
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            transform: translateX(4px);
        }
        .sidebar-link.active {
            box-shadow: inset 4px 0 0 0 rgba(255,255,255,0.8);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    
    <div class="flex min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
        
        <!-- Mobile Toggle Button -->
        <button @click="sidebarOpen = true" class="fixed bottom-6 right-6 z-40 lg:hidden w-14 h-14 rounded-full bg-indigo-600 text-white shadow-lg flex items-center justify-center hover:bg-indigo-700 transition-colors">
            <i class="bi bi-list text-xl"></i>
        </button>

        <!-- Sidebar -->
        <aside x-show="sidebarOpen" x-transition:enter="sidebar-transition" 
               x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="sidebar-transition" 
               x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white lg:static lg:inset-0 shadow-xl">
            <div class="flex items-center justify-between p-4 border-b border-indigo-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center">
                        <i class="bi bi-lightning-charge-fill text-indigo-600 text-lg"></i>
                    </div>
                    <h2 class="text-lg font-bold tracking-tight">{{ config('app.name', 'Laravel') }}</h2>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden focus:outline-none text-gray-300 hover:text-white">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            
            <div class="p-4 border-b border-indigo-700">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" 
                             class="w-10 h-10 rounded-full border-2 border-indigo-400" alt="User">
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-indigo-800"></span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-200">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            
            <nav class="mt-4 space-y-1 px-2">
                <a href="{{ route('admin.admin.dashboard') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.dashboard') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-speedometer2 mr-3 text-indigo-300"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.ads.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.ads.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-megaphone-fill mr-3 text-indigo-300"></i>
                    <span>Advertisements</span>
                </a>
                <a href="{{ route('admin.products.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.products.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-box-seam mr-3 text-indigo-300"></i>
                    <span>Products</span>
                    <span class="ml-auto px-2 py-1 text-xs font-bold rounded-full bg-indigo-600/50">New</span>
                </a>
                <a href="{{ route('admin.email-campaigns.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.email-campaigns.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-envelope-paper-fill mr-3 text-indigo-300"></i>
                    <span>Email Campaigns</span>
                </a>
                <a href="{{ route('admin.subscribers.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.subscribers.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-people-fill mr-3 text-indigo-300"></i>
                    <span>Subscribers</span>
                    <span class="ml-auto px-2 py-1 text-xs font-bold rounded-full bg-indigo-600/50">+24</span>
                </a>
                <a href="{{ route('admin.analytics.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.analytics.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill mr-3 text-indigo-300"></i>
                    <span>Analytics</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.payments.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill mr-3 text-indigo-300"></i>
                    <span>Payments</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-indigo-700">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center px-4 py-2 text-sm font-medium text-indigo-200 hover:text-white rounded-lg hover:bg-indigo-700/50">
                    <i class="bi bi-box-arrow-right mr-3"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        
        <div class="flex-1 flex flex-col lg:ml-64 transition-all duration-300">
            <!-- Header -->
            @include('layouts.adminNavigation')

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')

                </div>
            </main>
        </div>

        <!-- Overlay for mobile sidebar -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition:enter="overlay-transition" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="overlay-transition" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
    </div>

    <script>
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('aside');
            const toggleBtn = document.querySelector('[x-on\\:click="sidebarOpen = true"]');
            
            if (window.innerWidth < 1024 && 
                !sidebar.contains(event.target) && 
                event.target !== toggleBtn && 
                !toggleBtn.contains(event.target)) {
                Alpine.store('sidebarOpen', false);
            }
        });
    </script>
</body>
</html>