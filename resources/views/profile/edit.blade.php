@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 mt-8 to-indigo-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section with Back Button -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Back Button -->
                        <a href="{{ url()->previous() }}" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            {{ __('Back') }}
                        </a>
                        
                        <!-- Page Title -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ __('Profile Settings') }}</h1>
                            <p class="text-gray-600 mt-1">{{ __('Manage your account settings and preferences') }}</p>
                        </div>
                    </div>
                    
                    <!-- User Avatar & Quick Info -->
                <div class="flex items-center space-x-3 bg-white rounded-xl p-3 shadow-sm border border-gray-200">
                    @if(Auth::user()->profile_path)
                        <img class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200"
                            src="{{ asset('storage/' . Auth::user()->profile_path) }}"
                            alt="{{ Auth::user()->name }}">
                    @else
                        <img class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200"
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF"
                            alt="{{ Auth::user()->name }}">
                    @endif
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                </div>
            </div>

            <!-- Success Messages -->
            @if (session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Sidebar - Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 sticky top-12">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('Navigation') }}
                        </h3>
                        
                        <nav class="space-y-2">
                            <a href="#basic-info" 
                               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Basic Information') }}
                            </a>
                            
                            @if(Auth::user()->hasRole('instructor') || Auth::user()->isInstructor())
                            <a href="#instructor-profile" 
                               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-green-50 hover:text-green-700 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ __('Instructor Profile') }}
                            </a>
                            @endif
                            
                            <a href="#preferences" 
                               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ __('Preferences') }}
                            </a>
                            
                            <a href="#security" 
                               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-red-50 hover:text-red-700 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('Security') }}
                            </a>
                            
                            <a href="#danger-zone" 
                               class="flex items-center px-4 py-3 text-sm font-medium text-red-700 rounded-xl hover:bg-red-50 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                {{ __('Danger Zone') }}
                            </a>
                        </nav>
                        
                        <!-- Quick Stats -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                             <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <i class="fas fa-person mr-2 text-indigo-400"></i>
                            {{ __('Public Profile') }}
                        </h3>

                           @if(Auth::user()->hasRole('instructor') || Auth::user()->isInstructor())
                            <a href="{{ route('instructor.profile', $instructor->user_id) }}" 
                               class="flex items-center px-4 py-3 text-sm text-blue-600 font-medium rounded-xl hover:bg-blue-50 hover:text-green-700 transition-all duration-200 group">         
                                {{ __('My Public Profile') }}
                            </a>
                            @endif

                            {{-- @if(!Auth::user()->hasRole('instructor') || !Auth::user()->isInstructor())
                            <a href="{{ route('user.profile', $instructor->id) }}" 
                               class="flex items-center px-4 py-3 text-sm text-blue-600 font-medium rounded-xl hover:bg-blue-50 hover:text-green-700 transition-all duration-200 group">         
                                {{ __('My Public Profile') }}
                            </a>
                            @endif --}}
                            
                            
                            <h4 class="mt-8 pt-6 border-t border-gray-200 text-lg font-semibold text-gray-900 mb-1 flex items-center"><i class="fas fa-user mr-2 text-indigo-400"></i>{{ __('Account Overview') }}</h4>
                            <div class="px-4 py-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Member since</span>
                                    <span class="font-medium text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</span>
                                </div>
                                @if(Auth::user()->hasRole('instructor') || Auth::user()->isInstructor())
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Instructor</span>
                                    <span class="font-medium text-green-600">Verified</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Forms -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Basic Information Card -->
                    <div id="basic-info" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Basic Information') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Instructor Profile Card -->
                    @if(Auth::user()->hasRole('instructor') || Auth::user()->isInstructor())
                    <div id="instructor-profile" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ __('Instructor Profile') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.instructor_additional_info')
                        </div>
                    </div>
                    @endif

                    <!-- Preferences Card -->
                    <div id="preferences" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                {{ __('Preferences & Interests') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-additional-information-form', ['categories' => $categories])
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div id="security" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('Security Settings') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Danger Zone Card -->
                    <div id="danger-zone" class="bg-white rounded-2xl shadow-lg border border-red-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-600 to-rose-700 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                {{ __('Danger Zone') }}
                            </h2>
                        </div>
                        <div class="p-6 bg-red-50">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Smooth Scroll Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add active state to navigation
            const sections = document.querySelectorAll('div[id]');
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            
            window.addEventListener('scroll', function() {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 100;
                    const sectionHeight = section.clientHeight;
                    if (scrollY >= sectionTop) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('bg-indigo-100', 'text-indigo-700', 'bg-green-100', 'text-green-700', 'bg-blue-100', 'text-blue-700', 'bg-red-100', 'text-red-700');
                    if (link.getAttribute('href') === `#${current}`) {
                        const href = link.getAttribute('href');
                        if (href === '#instructor-profile') {
                            link.classList.add('bg-green-100', 'text-green-700');
                        } else if (href === '#preferences') {
                            link.classList.add('bg-blue-100', 'text-blue-700');
                        } else if (href === '#security' || href === '#danger-zone') {
                            link.classList.add('bg-red-100', 'text-red-700');
                        } else {
                            link.classList.add('bg-indigo-100', 'text-indigo-700');
                        }
                    }
                });
            });
        });
    </script>

    <style>
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a5b4fc;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Card hover effects */
        .bg-white {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .bg-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
@endsection