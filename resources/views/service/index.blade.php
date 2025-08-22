@extends('layouts.app')

@section('content')
    <title>Our Services - Financial Excellence</title>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0D47A1',
                        secondary: '#1565C0',
                        accent: '#FF6B35',
                        gold: '#FFD700',
                        dark: '#0A0E27'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slide-up 0.8s ease-out',
                        'fade-in': 'fade-in 1s ease-out',
                        'scale-in': 'scale-in 0.6s ease-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        'slide-up': {
                            '0%': { transform: 'translateY(50px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        'fade-in': {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        'scale-in': {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(255, 107, 53, 0.3);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #0D47A1, #FF6B35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 25px rgba(255, 107, 53, 0.5);
        }

      li{
            color: rgb(142, 170, 218);
        }


      
    </style>
</head>
<body class="bg-gradient-to-br from-dark via-gray-900 to-primary text-white">
  
        <!-- Hero Section -->
        <section class="relative py-20 px-4 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-accent/20"></div>
            
            <div class="container mx-auto text-center relative z-10 max-w-4xl">
                <div class="animate-fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 text-gradient">
                        Our Expert Services
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed">
                        Transform your financial future with our comprehensive digital products and personalized expert guidance
                    </p>
                    <div class="flex justify-center items-center space-x-8 animate-float">
                        <i class="fas fa-chart-line text-3xl text-gold"></i>
                        <i class="fas fa-handshake text-3xl text-accent"></i>
                        <i class="fas fa-trophy text-3xl text-secondary"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Grid -->
        <section class="py-16 px-6">
            <div class="container mx-auto max-w-6xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Digital Products -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-slide-up">
                        <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-laptop-code text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Digital Products</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Cutting-edge financial tools and calculators designed to simplify complex investment decisions and portfolio management.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Investment Portfolio Analyzer
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Risk Assessment Tools
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Retirement Planning Calculator
                            </li>
                        </ul>
                        <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>

                    </div>

                    <!-- Personal Consultation -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-slide-up">
                        <div class="w-16 h-16 bg-gradient-to-r from-accent to-orange-600 rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-user-tie text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Personal Consultation</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            One-on-one sessions with certified financial advisors to create personalized strategies for your unique goals.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                60-minute Strategy Sessions
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Custom Action Plans
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Ongoing Support & Follow-up
                            </li>
                        </ul>
                        <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>
                    </div>

                    <!-- Investment Training -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-slide-up">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-graduation-cap text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Investment Training</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Comprehensive courses and workshops to master the fundamentals of investing and advanced trading strategies.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Beginner to Advanced Courses
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Live Trading Workshops
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Certificate Programs
                            </li>
                        </ul>
                         <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>
                    </div>

                    <!-- Portfolio Management -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-scale-in">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-chart-pie text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Portfolio Management</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Professional portfolio management services with continuous monitoring and optimization for maximum returns.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Diversification Strategies
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Monthly Performance Reports
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Risk Management
                            </li>
                        </ul>
                         <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>
                    </div>

                    <!-- Financial Planning -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-scale-in">
                        <div class="w-16 h-16 bg-gradient-to-r from-yellow-600 to-orange-500 rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-calculator text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Financial Planning</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Comprehensive financial planning services covering budgeting, debt management, and long-term wealth building.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Budget Optimization
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Debt Elimination Plans
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Wealth Building Strategies
                            </li>
                        </ul>
                         <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>
                    </div>

                    <!-- Tax Optimization -->
                    <div class="service-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 animate-scale-in">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 animate-float">
                            <i class="fas fa-file-invoice-dollar text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gradient">Tax Optimization</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Strategic tax planning services to minimize your tax liability and maximize your after-tax returns legally.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Tax-Loss Harvesting
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Retirement Account Optimization
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-400 mr-3"></i>
                                Annual Tax Reviews
                            </li>
                        </ul>
                        <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 px-6">
            <div class="container mx-auto max-w-4xl text-center">
                <div class="glass-effect rounded-3xl p-12 animate-fade-in">
                    <h2 class="text-4xl font-bold mb-6 text-gradient">Ready to Transform Your Financial Future?</h2>
                    <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                        Join thousands of clients who have achieved financial excellence with our proven strategies and expert guidance.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                         <a href="/contact" class="w-full bg-primary hover:bg-secondary py-3 rounded-xl font-semibold transition-all btn-glow rounded-xl inline-block text-center">
                            Get Started Today
                        </a>
                        {{-- <button class="px-8 py-4 bg-transparent border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-full font-bold text-lg transition-all">
                            Schedule Consultation
                        </button> --}}
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Info -->
        <section class="py-12 px-6 border-t border-gray-800">
            <div class="container mx-auto max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="animate-slide-up">
                        <i class="fas fa-phone text-3xl text-accent mb-4"></i>
                        <h3 class="text-lg font-semibold mb-2">Call Us</h3>
                        <p class="text-gray-400">+ (680) 834 767</p>
                    </div>
                    <div class="animate-slide-up">
                        <i class="fas fa-envelope text-3xl text-gold mb-4"></i>
                        <h3 class="text-lg font-semibold mb-2">Email Us</h3>
                        <p class="text-gray-400">info@financialexcellence.com</p>
                    </div>
                    <div class="animate-slide-up">
                        <i class="fas fa-calendar-alt text-3xl text-secondary mb-4"></i>
                        <h3 class="text-lg font-semibold mb-2">Availability</h3>
                        <p class="text-gray-400">Mon-Fri: 9AM-6PM EST</p>
                    </div>
                </div>
            </div>
        </section>
 

    <script>
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = Math.random() * 0.2 + 's';
                    entry.target.classList.add('opacity-100');
                }
            });
        }, observerOptions);

        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.animate-slide-up, .animate-scale-in, .animate-fade-in');
            animatedElements.forEach(el => {
                el.classList.add('opacity-0');
                observer.observe(el);
            });

            // Add hover effects to service cards
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
@endsection