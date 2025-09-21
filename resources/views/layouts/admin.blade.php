<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>


    <!-- In your layouts/admin.blade.php head section -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .overlay-transition { transition: opacity 0.3s ease-in-out; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { transform: translateX(4px); }
        .sidebar-link.active { box-shadow: inset 4px 0 0 0 rgba(255,255,255,0.8); }
        
        /* Fixed layout styles */
        body {
            overflow-x: hidden;
        }
        
        /* Fixed sidebar */
        .sidebar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 40;
            width: 16rem; /* 64px equivalent to w-64 */
        }
        
        /* Fixed navbar */
        .navbar-fixed {
            position: fixed;
            top: 0;
            right: 0;
            left: 16rem; /* Match sidebar width */
            z-index: 30;
            height: 4rem; /* Approx h-16 */
        }
        
        /* Main content area */
        .main-content {
            margin-left: 16rem; /* Match sidebar width */
            margin-top: 4rem; /* Match navbar height */
            width: calc(100% - 16rem);
            min-height: calc(100vh - 4rem);
            overflow-y: auto;
        }
        
        /* Mobile adjustments */
        @media (max-width: 1023px) {
            .sidebar-fixed {
                transform: translateX(-100%);
            }
            
            .sidebar-fixed.open {
                transform: translateX(0);
            }
            
            .navbar-fixed {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
         @resize.window="sidebarOpen = window.innerWidth >= 1024">

        <!-- Floating Toggle Button (Mobile Only) -->
        <button @click="sidebarOpen = true" 
                class="fixed bottom-6 right-6 z-50 lg:hidden w-14 h-14 rounded-full bg-indigo-600 text-white shadow-lg flex items-center justify-center hover:bg-indigo-700 transition">
            <i class="bi bi-list text-2xl"></i>
        </button>

        <!-- Sidebar -->
        <aside :class="{'open': sidebarOpen}"
               class="sidebar-fixed bg-gradient-to-b from-indigo-800 to-indigo-900 text-white shadow-xl flex flex-col transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-indigo-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center">
                        <i class="bi bi-lightning-charge-fill text-indigo-600 text-lg"></i>
                    </div>
                    <h2 class="text-lg font-bold tracking-tight">{{ config('app.name', 'Laravel') }}</h2>
                </div>
                <button @click="sidebarOpen = false" 
                        class="lg:hidden text-gray-300 hover:text-white">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- User -->
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

            <!-- Navigation -->
            <nav class="mt-4 space-y-1 px-2 flex-1 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.dashboard') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-speedometer2 mr-3 text-indigo-300"></i> Dashboard
                </a>
                 <a href="{{ route('admin.users.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.users.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-people mr-3 text-indigo-300"></i> All Users
                </a>
                <a href="{{ route('admin.ads.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.ads.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-megaphone-fill mr-3 text-indigo-300"></i> Advertisements
                </a>
               
                <a href="{{ route('admin.courses.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.courses.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-box-seam mr-3 text-indigo-300"></i> Courses
                    <span class="ml-auto px-2 py-1 text-xs font-bold rounded-full bg-indigo-600/50">New</span>
                </a>
                <a href="{{ route('admin.email-campaigns.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.email-campaigns.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-envelope-paper-fill mr-3 text-indigo-300"></i> Email Campaigns
                </a>
                {{-- <a href="{{ route('admin.subscribers.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.subscribers.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-people-fill mr-3 text-indigo-300"></i> Subscribers
                    <span class="ml-auto px-2 py-1 text-xs font-bold rounded-full bg-indigo-600/50">+24</span>
                </a> --}}
                {{-- <a href="{{ route('admin.analytics.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.analytics.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill mr-3 text-indigo-300"></i> Analytics
                </a> --}}
                {{-- <a href="{{ route('admin.payments.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-indigo-700/50 {{ Route::is('admin.payments.*') ? 'bg-indigo-700 active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill mr-3 text-indigo-300"></i> Payments
                </a> --}}
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-indigo-700">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center px-4 py-2 text-sm font-medium text-indigo-200 hover:text-white rounded-lg hover:bg-indigo-700/50">
                    <i class="bi bi-box-arrow-right mr-3"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Navigation Bar -->
        <div class="navbar-fixed bg-white shadow-sm transition-all">
            @include('layouts.adminNavigation')
        </div>

        <!-- Main Content -->
        <div class="main-content transition-all">
            <main class="p-4 sm:p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Overlay (Mobile) -->
        <div x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden transition-opacity"></div>
    </div>

    <script>
        // Handle sidebar state on resize and page load
        document.addEventListener('DOMContentLoaded', function() {
            // Close sidebar on mobile by default
            if (window.innerWidth < 1024) {
                Alpine.store('sidebarOpen', false);
            }
            
            // Close sidebar when clicking on a link (mobile)
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        Alpine.store('sidebarOpen', false);
                    }
                });
            });
        });
    </script>
</body>
</html>