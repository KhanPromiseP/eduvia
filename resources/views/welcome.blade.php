<x-guest-layout>
    <!-- Hero Section -->
    <section class="flex-1 flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#FDFDFC] to-gray-100">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                Achieve Financial Excellence with Our Digital Products & Expert Services
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Explore courses and eBooks on wealth building, read our blog for actionable financial tips, and contact us for personalized services.
            </p>
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
                  
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">5 Ways to Build Wealth in 2025</h3>
                        <p class="text-gray-600 mb-4">Discover strategies to grow your finances this year.</p>
                       
                    </div>
                </div>
                <!-- Blog Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                  
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Beginner's Guide to Smart Investing</h3>
                        <p class="text-gray-600 mb-4">Step-by-step tips for starting your investment journey.</p>
                        
                    </div>
                </div>
                <!-- Blog Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                   
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Budgeting Secrets for Financial Freedom</h3>
                        <p class="text-gray-600 mb-4">Learn how to manage your money effectively.</p>
                        
                    </div>
                </div>
            </div>
           
        </div>
    </section>

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
                        'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
                        'shake': 'shake 0.5s ease-in-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        'slide-up': {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        'fade-in': {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        'pulse-glow': {
                            '0%, 100%': { boxShadow: '0 0 5px rgba(255, 107, 53, 0.5)' },
                            '50%': { boxShadow: '0 0 20px rgba(255, 107, 53, 0.8)' }
                        },
                        'shake': {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '25%': { transform: 'translateX(-5px)' },
                            '75%': { transform: 'translateX(5px)' }
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
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.3);
            border-color: #FF6B35;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #0D47A1, #FF6B35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 30px rgba(255, 107, 53, 0.6);
        }
        
        .floating-icon {
            animation: float 4s ease-in-out infinite;
        }
        
        .floating-icon:nth-child(2) {
            animation-delay: 1s;
        }
        
        .floating-icon:nth-child(3) {
            animation-delay: 2s;
        }
        
        .form-field {
            transition: all 0.3s ease;
        }
        
        .form-field:hover {
            transform: translateY(-2px);
        }
        
        .success-message {
            background: linear-gradient(135deg, #10B981, #059669);
        }
        
        .error-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>


  
            
            <div class="container mx-auto text-center relative z-10 max-w-4xl">
                <div class="animate-fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 text-gradient">
                        Get In Touch
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                        Ready to transform your financial future? Let's discuss how our expert services can help you achieve your goals.
                    </p>
                    <a href="/contact" class="inline-block px-8 py-3 bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-full shadow-lg hover:bg-secondary transition-colors duration-300">
                        Contact Us
                    </a>
                </div>
            </div>
     

      
</body>
</x-guest-layout>
