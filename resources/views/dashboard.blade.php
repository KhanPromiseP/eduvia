@extends('layouts.app')

@section('content')
<body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">
    <!-- Hero Section with Animated Background -->
    <section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 text-white overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute animate-pulse-slow" style="top: 20%; left: 10%; width: 300px; height: 300px; background: radial-gradient(rgba(59, 130, 246, 0.4), transparent 70%);"></div>
            <div class="absolute animate-pulse-slower" style="top: 60%; right: 15%; width: 400px; height: 400px; background: radial-gradient(rgba(139, 92, 246, 0.3), transparent 70%);"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="text-center" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 300)" 
                 :class="{ 'opacity-0 translate-y-10': !visible, 'opacity-100 translate-y-0': visible }" 
                 class="transition-all duration-700 ease-out">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Transform Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Financial Future</span>
                </h1>
                <p class="text-xl sm:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto">
                    Master wealth building, investment strategies, and financial excellence with our expert-led courses and personalized services
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="#featured-courses" class="px-8 py-4 bg-white text-blue-900 font-bold rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1">
                        Explore Courses
                    </a>
                    <a href="#services" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-blue-900 transition-all duration-300">
                        Our Services
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Animated stats -->
        <div class="relative bg-white/10 backdrop-blur-sm py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { if (count < {{ $courses->count() }}) count++; else clearInterval(interval) }, 20) }, 500)" x-text="count"></div>
                        <p class="text-blue-200">Courses Available</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { if (count < 10000) count+=100; else clearInterval(interval) }, 1) }, 700)" x-text="count.toLocaleString() + '+'"></div>
                        <p class="text-blue-200">Students Enrolled</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.6s">
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { if (count < 50) count++; else clearInterval(interval) }, 30) }, 900)" x-text="count + '+'"></div>
                        <p class="text-blue-200">Expert Instructors</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.8s">
                        <div class="text-3xl md:text-4xl font-bold text-white mb-2" x-data="{ count: 0 }" x-init="setTimeout(() => { let interval = setInterval(() => { if (count < 95) count++; else clearInterval(interval) }, 20) }, 1100)" x-text="count + '%'"></div>
                        <p class="text-blue-200">Success Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses Carousel -->
    <section id="featured-courses" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Featured Courses</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Handpicked courses to kickstart your financial journey</p>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="px-6 py-2 rounded-full bg-blue-600 text-white font-semibold transition-all duration-300 transform hover:-translate-y-1">
                    All Courses
                </button>
                @foreach($categories->take(6) as $category)
                <button class="px-6 py-2 rounded-full border-2 border-gray-300 text-gray-700 font-semibold hover:border-blue-600 hover:text-blue-600 transition-all duration-300">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Courses Carousel -->
            <div class="relative">
                <div class="swiper featured-courses-swiper">
                    <div class="swiper-wrapper">
                        @foreach($courses as $course)
                        <div class="swiper-slide">
                            @include('components.course-card', ['course' => $course, 'featured' => true])
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Learning Paths Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Structured Learning Paths</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Follow curated paths to achieve your financial goals</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Wealth Building</h3>
                    <p class="text-gray-600 mb-6">Learn strategies to build and grow your wealth systematically</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">5 courses • 20 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Mastery</h3>
                    <p class="text-gray-600 mb-6">Master stock market, real estate, and alternative investments</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">8 courses • 35 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Financial Freedom</h3>
                    <p class="text-gray-600 mb-6">Achieve financial independence through smart planning</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">6 courses • 25 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses Grid -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Most Popular Courses</h2>
                    <p class="text-lg text-gray-600">Courses loved by our community of learners</p>
                </div>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Sort by: Popularity</option>
                        <option>Sort by: Newest</option>
                        <option>Sort by: Price</option>
                        <option>Sort by: Rating</option>
                    </select>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                @include('components.course-card', ['course' => $course, 'popular' => true])
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                    View All Courses
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 bg-gradient-to-r from-blue-900 to-purple-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Why Choose Our Platform?</h2>
                <p class="text-xl text-blue-200 max-w-2xl mx-auto">Experience learning that transforms your financial future</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-graduation-cap text-3xl text-blue-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Expert Instructors</h3>
                    <p class="text-blue-200">Learn from industry professionals with years of experience</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-3xl text-green-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Certified Content</h3>
                    <p class="text-blue-200">Industry-recognized certifications and accreditations</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-3xl text-purple-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Community Support</h3>
                    <p class="text-blue-200">Join a thriving community of like-minded learners</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-3xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Mobile Learning</h3>
                    <p class="text-blue-200">Learn anywhere, anytime on any device</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Success Stories</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Hear from our students who transformed their financial lives</p>
            </div>

            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    ED
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Emily Davis</h4>
                                    <p class="text-blue-600">Wealth Manager</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"The investment strategies course completely transformed my approach to wealth building. I've seen a 40% increase in my portfolio returns!"</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    MB
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Michael Brown</h4>
                                    <p class="text-blue-600">Entrepreneur</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"The financial planning course gave me the confidence to start my own business. I'm now generating six figures in revenue!"</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    SJ
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Sarah Johnson</h4>
                                    <p class="text-blue-600">Financial Analyst</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"The personalized coaching helped me achieve financial freedom. I paid off all my debts and built a solid investment portfolio!"</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Ready to Transform Your Financial Future?</h2>
            <p class="text-xl text-blue-100 mb-10">Join thousands of students who have already started their journey to financial freedom</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                    Browse All Courses
                </a>
                <a href="#services" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-blue-600 transition-all duration-300">
                    Get Personalized Help
                </a>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Latest Insights</h2>
                <p class="text-lg text-gray-600">Stay updated with the latest financial trends and strategies</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-blue-600 text-sm font-semibold rounded-full">Wealth</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">2025 Wealth Building Strategies</h3>
                        <p class="text-gray-600 mb-4">Discover the most effective strategies to grow your wealth in the coming year</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Jan 15, 2024</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-green-500 to-teal-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-green-600 text-sm font-semibold rounded-full">Investing</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">AI in Investment Analysis</h3>
                        <p class="text-gray-600 mb-4">How artificial intelligence is revolutionizing investment decision making</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Jan 12, 2024</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-purple-500 to-pink-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-purple-600 text-sm font-semibold rounded-full">Budgeting</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Zero-Based Budgeting Mastery</h3>
                        <p class="text-gray-600 mb-4">Learn how to implement zero-based budgeting for maximum financial control</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Jan 8, 2024</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Personalized Financial Services</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Get one-on-one guidance from our expert financial advisors</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Wealth Management</h3>
                    <p class="text-gray-600 mb-6">Comprehensive wealth management strategies tailored to your goals</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Portfolio optimization
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Risk assessment
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Tax planning
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Advisory</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on building and managing your investment portfolio</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Stock selection
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Market analysis
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Performance tracking
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Retirement Planning</h3>
                    <p class="text-gray-600 mb-6">Secure your financial future with comprehensive retirement planning</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Retirement savings
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Pension planning
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Estate planning
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-20 bg-blue-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Stay Updated with Financial Insights</h2>
            <p class="text-xl text-blue-100 mb-8">Get weekly tips and strategies delivered to your inbox</p>
            
            <form class="max-w-md mx-auto">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <button type="submit" class="px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300">
                        Subscribe
                    </button>
                </div>
            </form>
            <p class="text-sm text-blue-200 mt-4">No spam. Unsubscribe at any time.</p>
        </div>
    </section>

    <!-- JavaScript for animations and carousels -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper carousels
            const featuredSwiper = new Swiper('.featured-courses-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });

            const testimonialsSwiper = new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    <style>
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-pulse-slower {
            animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .swiper-button-next, .swiper-button-prev {
            color: #3b82f6;
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px;
        }
    </style>
</body>
@endsection