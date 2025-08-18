<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Excellence Blog - Digital Products & Expert Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        'gradient': 'gradient 8s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slide-up 0.6s ease-out',
                        'fade-in': 'fade-in 0.8s ease-out'
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': { 'background-position': '0% 50%' },
                            '50%': { 'background-position': '100% 50%' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        'slide-up': {
                            '0%': { transform: 'translateY(100px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        'fade-in': {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(-45deg, #0D47A1, #1565C0, #FF6B35, #FFD700);
            background-size: 400% 400%;
            animation: gradient 8s ease infinite;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .blog-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .blog-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #0D47A1, #FF6B35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 30px rgba(255, 107, 53, 0.4);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-dark via-gray-900 to-primary text-white min-h-screen">
    <x-app-layout>
        <!-- Hero Section -->
        <section class="relative py-20 px-4 overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-20"></div>
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            
            <div class="container mx-auto text-center relative z-10">
                <h1 class="text-6xl md:text-8xl font-bold mb-6 text-gradient animate-slide-up">
                    Financial Excellence
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-300 animate-fade-in max-w-3xl mx-auto">
                    Master your finances with our cutting-edge digital products and expert services. 
                    Transform your financial future today.
                </p>
                <div class="flex justify-center items-center space-x-6 animate-float">
                    <i class="fas fa-chart-line text-4xl text-gold"></i>
                    <i class="fas fa-coins text-4xl text-accent"></i>
                    <i class="fas fa-piggy-bank text-4xl text-secondary"></i>
                </div>
            </div>
        </section>

        <!-- Blog Navigation -->
        <nav class="sticky top-0 z-50 glass-effect py-4 px-6 border-b border-gray-700">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex space-x-6">
                    <button onclick="showBlogList()" class="px-6 py-2 bg-primary hover:bg-secondary rounded-full transition-all duration-300 hover-glow font-semibold">
                        <i class="fas fa-list mr-2"></i>All Blogs
                    </button>
                    <div class="flex space-x-4">
                        <span class="px-4 py-2 bg-gray-800 rounded-full text-sm cursor-pointer hover:bg-gray-700 transition-all">Investment</span>
                        <span class="px-4 py-2 bg-gray-800 rounded-full text-sm cursor-pointer hover:bg-gray-700 transition-all">Trading</span>
                        <span class="px-4 py-2 bg-gray-800 rounded-full text-sm cursor-pointer hover:bg-gray-700 transition-all">Budgeting</span>
                        <span class="px-4 py-2 bg-gray-800 rounded-full text-sm cursor-pointer hover:bg-gray-700 transition-all">Crypto</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <i class="fas fa-search text-xl cursor-pointer hover:text-accent transition-colors"></i>
                    <i class="fas fa-user-circle text-xl cursor-pointer hover:text-accent transition-colors"></i>
                </div>
            </div>
        </nav>

        <!-- Blog List View -->
        <div id="blog-list" class="container mx-auto px-6 py-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 text-gradient">Latest Financial Insights</h2>
                <p class="text-gray-400 text-lg">Stay ahead with our expert analysis and proven strategies</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Featured Blog Card -->
                <div class="md:col-span-2 lg:col-span-3 blog-card bg-gradient-to-r from-primary to-secondary rounded-3xl overflow-hidden cursor-pointer" onclick="showBlogDetail(1)">
                    <div class="flex flex-col lg:flex-row">
                        <div class="lg:w-1/2 p-8 lg:p-12">
                            <div class="flex items-center mb-4">
                                <span class="bg-accent px-3 py-1 rounded-full text-sm font-semibold">Featured</span>
                                <span class="ml-4 text-gray-300">Dec 15, 2024</span>
                            </div>
                            <h3 class="text-3xl lg:text-4xl font-bold mb-4">The Ultimate Guide to Cryptocurrency Investment in 2025</h3>
                            <p class="text-lg text-gray-200 mb-6">Discover the strategies that top investors use to navigate the crypto market and build lasting wealth in the digital age.</p>
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face" class="w-12 h-12 rounded-full mr-4" alt="Author">
                                <div>
                                    <p class="font-semibold">Marcus Chen</p>
                                    <p class="text-gray-300 text-sm">Senior Financial Analyst</p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:w-1/2">
                            <img src="https://images.unsplash.com/photo-1640340434855-6084b1f4901c?w=600&h=400&fit=crop" class="w-full h-64 lg:h-full object-cover" alt="Cryptocurrency">
                        </div>
                    </div>
                </div>

                <!-- Regular Blog Cards -->
                <div class="blog-card bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750" onclick="showBlogDetail(2)">
                    <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=400&h=250&fit=crop" class="w-full h-48 object-cover" alt="Stock Market">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="bg-blue-600 px-3 py-1 rounded-full text-xs font-semibold">Trading</span>
                            <span class="ml-3 text-gray-400 text-sm">Dec 12, 2024</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Day Trading Strategies That Actually Work</h3>
                        <p class="text-gray-400 mb-4 text-sm">Learn the proven techniques used by professional traders to consistently profit in volatile markets.</p>
                        <div class="flex items-center">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face" class="w-8 h-8 rounded-full mr-3" alt="Author">
                            <span class="text-sm text-gray-300">Sarah Williams</span>
                        </div>
                    </div>
                </div>

                <div class="blog-card bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750" onclick="showBlogDetail(3)">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=250&fit=crop" class="w-full h-48 object-cover" alt="Budget Planning">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="bg-green-600 px-3 py-1 rounded-full text-xs font-semibold">Budgeting</span>
                            <span class="ml-3 text-gray-400 text-sm">Dec 10, 2024</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">50/30/20 Rule: Modern Budget Mastery</h3>
                        <p class="text-gray-400 mb-4 text-sm">Master the art of budgeting with this time-tested formula that ensures financial stability and growth.</p>
                        <div class="flex items-center">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face" class="w-8 h-8 rounded-full mr-3" alt="Author">
                            <span class="text-sm text-gray-300">David Rodriguez</span>
                        </div>
                    </div>
                </div>

                <div class="blog-card bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750" onclick="showBlogDetail(4)">
                    <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=400&h=250&fit=crop" class="w-full h-48 object-cover" alt="Real Estate">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="bg-purple-600 px-3 py-1 rounded-full text-xs font-semibold">Investment</span>
                            <span class="ml-3 text-gray-400 text-sm">Dec 8, 2024</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Real Estate vs. Stock Market: 2025 Analysis</h3>
                        <p class="text-gray-400 mb-4 text-sm">Compare the pros and cons of real estate and stock investments in today's economic climate.</p>
                        <div class="flex items-center">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face" class="w-8 h-8 rounded-full mr-3" alt="Author">
                            <span class="text-sm text-gray-300">Emma Thompson</span>
                        </div>
                    </div>
                </div>

                <div class="blog-card bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750" onclick="showBlogDetail(5)">
                    <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=400&h=250&fit=crop" class="w-full h-48 object-cover" alt="Retirement Planning">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="bg-yellow-600 px-3 py-1 rounded-full text-xs font-semibold">Planning</span>
                            <span class="ml-3 text-gray-400 text-sm">Dec 5, 2024</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Retirement Planning in Your 30s</h3>
                        <p class="text-gray-400 mb-4 text-sm">Start building your retirement fund early with these proven strategies and investment vehicles.</p>
                        <div class="flex items-center">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face" class="w-8 h-8 rounded-full mr-3" alt="Author">
                            <span class="text-sm text-gray-300">Alex Johnson</span>
                        </div>
                    </div>
                </div>

                <div class="blog-card bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750" onclick="showBlogDetail(6)">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=250&fit=crop" class="w-full h-48 object-cover" alt="Tax Planning">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <span class="bg-red-600 px-3 py-1 rounded-full text-xs font-semibold">Tax</span>
                            <span class="ml-3 text-gray-400 text-sm">Dec 3, 2024</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Tax Optimization Strategies for 2025</h3>
                        <p class="text-gray-400 mb-4 text-sm">Maximize your tax savings with these legal strategies and deductions you might be missing.</p>
                        <div class="flex items-center">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face" class="w-8 h-8 rounded-full mr-3" alt="Author">
                            <span class="text-sm text-gray-300">Lisa Park</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-12">
                <button class="px-8 py-4 bg-gradient-to-r from-accent to-orange-600 rounded-full font-semibold hover:shadow-lg hover:shadow-accent/25 transition-all duration-300 transform hover:scale-105">
                    Load More Articles
                </button>
            </div>
        </div>

        <!-- Single Blog View -->
        <div id="blog-detail" class="hidden">
            <div class="container mx-auto px-6 py-12 max-w-4xl">
                <button onclick="showBlogList()" class="mb-8 flex items-center text-accent hover:text-orange-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Blogs
                </button>

                <article class="bg-gray-800 rounded-3xl overflow-hidden">
                    <div class="relative h-96 overflow-hidden">
                        <img id="blog-image" src="" class="w-full h-full object-cover" alt="">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-8 left-8">
                            <span id="blog-category" class="bg-accent px-4 py-2 rounded-full text-sm font-semibold"></span>
                            <h1 id="blog-title" class="text-4xl lg:text-5xl font-bold mt-4 text-white"></h1>
                        </div>
                    </div>

                    <div class="p-8 lg:p-12">
                        <div class="flex items-center mb-8">
                            <img id="author-image" src="" class="w-16 h-16 rounded-full mr-4" alt="">
                            <div>
                                <p id="author-name" class="font-bold text-lg"></p>
                                <p id="author-title" class="text-gray-400"></p>
                                <p id="blog-date" class="text-gray-500 text-sm"></p>
                            </div>
                        </div>

                        <div id="blog-content" class="prose prose-lg prose-invert max-w-none">
                            <!-- Content will be loaded here -->
                        </div>

                        <div class="mt-12 pt-8 border-t border-gray-700">
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-4 py-2 bg-primary rounded-full text-sm">#FinancialPlanning</span>
                                <span class="px-4 py-2 bg-secondary rounded-full text-sm">#Investment</span>
                                <span class="px-4 py-2 bg-accent rounded-full text-sm">#WealthBuilding</span>
                                <span class="px-4 py-2 bg-purple-600 rounded-full text-sm">#MoneyManagement</span>
                            </div>
                            
                            <div class="flex space-x-4">
                                <button class="flex items-center space-x-2 px-6 py-3 bg-blue-600 rounded-full hover:bg-blue-700 transition-colors">
                                    <i class="fab fa-facebook-f"></i>
                                    <span>Share</span>
                                </button>
                                <button class="flex items-center space-x-2 px-6 py-3 bg-sky-500 rounded-full hover:bg-sky-600 transition-colors">
                                    <i class="fab fa-twitter"></i>
                                    <span>Tweet</span>
                                </button>
                                <button class="flex items-center space-x-2 px-6 py-3 bg-blue-800 rounded-full hover:bg-blue-900 transition-colors">
                                    <i class="fab fa-linkedin-in"></i>
                                    <span>Share</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </x-app-layout>

    <script>
        // Blog data
        const blogs = {
            1: {
                title: "The Ultimate Guide to Cryptocurrency Investment in 2025",
                category: "Featured",
                image: "https://images.unsplash.com/photo-1640340434855-6084b1f4901c?w=800&h=400&fit=crop",
                author: "Marcus Chen",
                authorTitle: "Senior Financial Analyst",
                authorImage: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face",
                date: "December 15, 2024",
                content: `
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">The cryptocurrency market has evolved dramatically since Bitcoin's inception. As we enter 2025, the landscape presents both unprecedented opportunities and unique challenges that require a sophisticated approach to investment.</p>
                    
                    <h2 class="text-3xl font-bold mb-6 text-gradient">Understanding the Current Market Dynamics</h2>
                    <p class="mb-6">The crypto market in 2025 is characterized by increased institutional adoption, regulatory clarity, and technological innovations. Major corporations now hold Bitcoin on their balance sheets, while governments worldwide have established clearer frameworks for digital asset regulation.</p>
                    
                    <div class="bg-gradient-to-r from-primary/20 to-secondary/20 rounded-2xl p-8 mb-8 border border-blue-500/30">
                        <h3 class="text-2xl font-bold mb-4 flex items-center">
                            <i class="fas fa-lightbulb text-gold mr-3"></i>
                            Key Investment Principles
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start"><i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>Diversify across different cryptocurrency categories</li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>Understand the technology behind your investments</li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>Implement proper risk management strategies</li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>Stay informed about regulatory developments</li>
                        </ul>
                    </div>
                    
                    <h2 class="text-3xl font-bold mb-6 text-gradient">Portfolio Allocation Strategies</h2>
                    <p class="mb-6">A well-balanced cryptocurrency portfolio in 2025 should consider allocation across established cryptocurrencies like Bitcoin and Ethereum, emerging altcoins with strong fundamentals, and DeFi protocols that demonstrate sustainable value creation.</p>
                    
                    <p class="mb-6">The recommended allocation for most investors follows the 60-30-10 rule: 60% in established cryptocurrencies, 30% in promising altcoins, and 10% in high-risk, high-reward experimental projects.</p>
                    
                    <h2 class="text-3xl font-bold mb-6 text-gradient">Risk Management in Crypto Investing</h2>
                    <p class="mb-6">Cryptocurrency investing requires robust risk management due to the market's inherent volatility. Never invest more than you can afford to lose, and always maintain a diversified portfolio that includes traditional assets alongside your crypto holdings.</p>
                    
                    <p class="text-lg font-semibold text-accent">Remember: The key to successful cryptocurrency investing lies not in timing the market, but in time in the market combined with disciplined risk management.</p>
                `
            },
            2: {
                title: "Day Trading Strategies That Actually Work",
                category: "Trading",
                image: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&h=400&fit=crop",
                author: "Sarah Williams",
                authorTitle: "Professional Day Trader",
                authorImage: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face",
                date: "December 12, 2024",
                content: `
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">Day trading can be one of the most lucrative yet challenging forms of active investing. Success requires discipline, strategy, and a deep understanding of market psychology.</p>
                    
                    <h2 class="text-3xl font-bold mb-6 text-gradient">The Foundation of Successful Day Trading</h2>
                    <p class="mb-6">Before diving into specific strategies, it's crucial to understand that day trading success is built on three pillars: technical analysis, risk management, and emotional control. Without mastering these fundamentals, even the best strategies will fail.</p>
                    
                    <div class="bg-gradient-to-r from-accent/20 to-orange-600/20 rounded-2xl p-8 mb-8 border border-orange-500/30">
                        <h3 class="text-2xl font-bold mb-4 flex items-center">
                            <i class="fas fa-chart-line text-gold mr-3"></i>
                            Top Day Trading Strategies
                        </h3>
                        <ul class="space-y-4">
                            <li><strong>Scalping:</strong> Quick trades lasting seconds to minutes, focusing on small price movements</li>
                            <li><strong>Momentum Trading:</strong> Following strong price movements with high volume</li>
                            <li><strong>Range Trading:</strong> Buying at support and selling at resistance levels</li>
                            <li><strong>News-Based Trading:</strong> Capitalizing on market reactions to news events</li>
                        </ul>
                    </div>
                    
                    <h2 class="text-3xl font-bold mb-6 text-gradient">Risk Management Techniques</h2>
                    <p class="mb-6">The 1% rule is sacred in day trading: never risk more than 1% of your trading capital on a single trade. This ensures that even a series of losses won't devastate your account. Additionally, always set stop-losses before entering a trade, not after you're already losing money.</p>
                    
                    <p class="text-lg font-semibold text-accent">Success in day trading comes from being consistently profitable over time, not from hitting home runs on individual trades.</p>
                `
            }
        };

        function showBlogList() {
            document.getElementById('blog-list').classList.remove('hidden');
            document.getElementById('blog-detail').classList.add('hidden');
        }

        function showBlogDetail(blogId) {
            const blog = blogs[blogId];
            if (!blog) return;

            document.getElementById('blog-image').src = blog.image;
            document.getElementById('blog-category').textContent = blog.category;
            document.getElementById('blog-title').textContent = blog.title;
            document.getElementById('author-image').src = blog.authorImage;
            document.getElementById('author-name').textContent = blog.author;
            document.getElementById('author-title').textContent = blog.authorTitle;
            document.getElementById('blog-date').textContent = blog.date;
            document.getElementById('blog-content').innerHTML = blog.content;

            document.getElementById('blog-list').classList.add('hidden');
            document.getElementById('blog-detail').classList.remove('hidden');
            
            window.scrollTo(0, 0);
        }

        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = Math.random() * 0.3 + 's';
                    entry.target.classList.add('animate-slide-up');
                }
            });
        }, observerOptions);

        document.addEventListener('DOMContentLoaded', () => {
            const blogCards = document.querySelectorAll('.blog-card');
            blogCards.forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>