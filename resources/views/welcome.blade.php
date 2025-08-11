<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Discover digital products and courses for financial excellence. Read our blog on wealth building and contact us for personalized services.">
    <meta name="keywords" content="financial excellence, digital products, wealth building blog, financial services, courses, eBooks">
    <meta name="author" content="{{ config('app.name', 'Laravel') }}">

    <title>{{ config('app.name', 'Laravel') }} - Unlock Financial Success</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">
    <!-- Sticky Navigation -->
    <header class="sticky top-0 bg-white shadow-sm z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 transition-colors shadow-sm hover:shadow-md">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 transition-colors shadow-sm hover:shadow-md">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </nav>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden">
                    <button @click="menuOpen = !menuOpen" class="text-gray-700 focus:outline-none">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="menuOpen" x-transition class="lg:hidden mt-4 pb-4 border-t border-gray-200">
                <nav class="flex flex-col space-y-3 px-4">
                   
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block px-5 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-5 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#FDFDFC] to-gray-100">
        <div class="max-w-5xl mx-auto text-center" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 300)" :class="{ 'opacity-0 translate-y-10': !visible, 'opacity-100 translate-y-0': visible }" class="transition-all duration-700 ease-out">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                Achieve Financial Excellence with Our Digital Products & Expert Services
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Explore courses and eBooks on wealth building, read our blog for actionable financial tips, and contact us for personalized services to elevate your success.
            </p>
            
        </div>
    </main>

    <!-- Digital Products Section -->
    <section id="products" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Our Digital Products for Financial Mastery</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Discover courses and eBooks designed to help you excel financially, from investment strategies to wealth building fundamentals.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Product Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Wealth+Building+Course" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Wealth Building Course</h3>
                        <p class="text-gray-600 mb-4">Learn proven strategies to build and grow your wealth in 2025.</p>
                        <p class="text-indigo-600 font-bold mb-4">$49.99</p>
                       
                    </div>
                </div>
                <!-- Product Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Investment+eBook" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Investment Essentials eBook</h3>
                        <p class="text-gray-600 mb-4">Expert tips on investing for long-term financial success.</p>
                        <p class="text-indigo-600 font-bold mb-4">$19.99</p>
                        
                    </div>
                </div>
                <!-- Product Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Budgeting+Masterclass" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Budgeting Masterclass</h3>
                        <p class="text-gray-600 mb-4">Master budgeting to achieve financial freedom.</p>
                        <p class="text-indigo-600 font-bold mb-4">$29.99</p>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Blog Teaser Section -->
    <section id="blog" class="py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Insights on Financial Excellence</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Read our blog for tips on wealth building, investment strategies, and achieving financial success.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Blog Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Wealth+Tips" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">5 Ways to Build Wealth in 2025</h3>
                        <p class="text-gray-600 mb-4">Discover strategies to grow your finances this year.</p>
                       
                    </div>
                </div>
                <!-- Blog Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Investment+Guide" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Beginner's Guide to Smart Investing</h3>
                        <p class="text-gray-600 mb-4">Step-by-step tips for starting your investment journey.</p>
                        
                    </div>
                </div>
                <!-- Blog Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Budgeting+Secrets" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Budgeting Secrets for Financial Freedom</h3>
                        <p class="text-gray-600 mb-4">Learn how to manage your money effectively.</p>
                        
                    </div>
                </div>
            </div>
           
        </div>
    </section>

    <!-- Services Section with Contact Form -->
    <section id="services" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Our Financial Services</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Get personalized advice on wealth management, investments, and financial planning. Contact us to get started.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Personalized Financial Consulting</h3>
                    <p class="text-gray-600 mb-6">Tailored strategies to help you achieve financial goals.</p>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Investment Advisory</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on smart investments for growth.</p>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Wealth Building Workshops</h3>
                    <p class="text-gray-600">Interactive sessions for financial literacy.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Contact Us for Services</h3>
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                            <textarea id="message" name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors"></textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">What Our Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"The financial courses transformed my approach to wealth building!"</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Emily Davis</h4>
                            <p class="text-gray-500 text-sm">Investor</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Excellent blog posts on investment strategies—highly recommend!"</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Michael Brown</h4>
                            <p class="text-gray-500 text-sm">Entrepreneur</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Personalized services helped me achieve financial goals faster."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Sarah Johnson</h4>
                            <p class="text-gray-500 text-sm">Client</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-10 px-4 sm:px-6 lg:px-8 border-t border-gray-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
            <p class="text-gray-600 text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            <nav class="flex space-x-6">
                <a href="/privacy" class="text-gray-600 hover:text-indigo-600 text-sm font-medium">Privacy Policy</a>
                <a href="/terms" class="text-gray-600 hover:text-indigo-600 text-sm font-medium">Terms of Service</a>
            </nav>
        </div>
    </footer>
</body>
</html>