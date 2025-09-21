@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Excellence Blog - Digital Products & Expert Services</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
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
            background: linear-gradient(-45deg,rgb(154, 168, 189),rgb(125, 182, 248),rgb(219, 184, 171),rgb(194, 189, 164));
            background-size: 400% 400%;
            animation: gradient 8s ease infinite;
        }
        
        .glass-effect {
            background: rgba(202, 174, 174, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(141, 126, 126, 0.2);
        }
        
        .blog-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .blog-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(168, 146, 146, 0.25);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #0D47A1, #FF6B35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 30px rgba(209, 178, 166, 0.4);
        }
        
        .admin-panel {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(194, 176, 176, 0.9);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-dark via-gray-900 to-primary text-white min-h-screen">
    <!-- Admin Button -->
@auth
    @if(auth()->user()->is_admin)  <!-- check your boolean column -->
        <button id="admin-toggle" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-accent rounded-full flex items-center justify-center hover:bg-orange-500 transition-all shadow-lg hover-glow">
            <i class="fas fa-cog text-xl"></i>
        </button>
    @endif
@endauth




    <!-- Admin Panel -->
    <div id="admin-panel" class="admin-panel">
        <div class="container mx-auto max-w-4xl bg-gray-200 rounded-2xl p-8 mt-10">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gradient">Blog Management</h2>
                <button id="close-admin" class="px-4 py-2 bg-red-600 rounded-full hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
            
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">Add New Blog</h3>
                <form id="blog-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" id="blog-id">
                    <div class="md:col-span-2">
                        <label class="block mb-2">Title</label>
                        <input type="text" id="blog-title-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Category</label>
                        <input type="text" id="blog-category-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Date</label>
                        <input type="date" id="blog-date-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Image URL</label>
                        <input type="url" id="blog-image-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Author Name</label>
                        <input type="text" id="blog-author-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Author Title</label>
                        <input type="text" id="blog-author-title-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-2">Author Image URL</label>
                        <input type="url" id="blog-author-image-input" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2">Content (HTML)</label>
                        <textarea id="blog-content-input" rows="10" required class="w-full p-3 bg-gray-100 rounded-lg border border-gray-600 focus:border-accent focus:outline-none"></textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-4">
                        <button type="button" id="cancel-edit" class="px-6 py-3 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors hidden">
                            Cancel Edit
                        </button>
                        <button type="submit" class="px-6 py-3 bg-accent rounded-full hover:bg-orange-500 transition-colors">
                            Save Blog
                        </button>
                    </div>
                </form>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-4">Existing Blogs</h3>
                <div id="blog-list-admin" class="space-y-4">
                    <!-- Blogs will be listed here -->
                </div>
            </div>
        </div>
    </div>

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
                    <span class="px-4 py-2 bg-gray-300 rounded-full text-sm cursor-pointer hover:bg-gray-500 transition-all">Investment</span>
                    <span class="px-4 py-2 bg-gray-300 rounded-full text-sm cursor-pointer hover:bg-gray-500 transition-all">Trading</span>
                    <span class="px-4 py-2 bg-gray-300 rounded-full text-sm cursor-pointer hover:bg-gray-500 transition-all">Budgeting</span>
                    <span class="px-4 py-2 bg-gray-300 rounded-full text-sm cursor-pointer hover:bg-gray-500 transition-all">Crypto</span>
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

        <div id="blog-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Blogs will be dynamically inserted here -->
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

            <article class="bg-gray-500 rounded-3xl overflow-hidden">
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

    <script>
        // Blog data storage
        const BLOG_STORAGE_KEY = 'financial_blogs';
        
        // Default blog data
        const defaultBlogs = {
            1: {
                id: 1,
                title: "The Ultimate Guide to Cryptocurrency Investment in 2025",
                category: "Featured",
                image: "https://images.unsplash.com/photo-1640340434855-6084b1f4901c?w=800&h=400&fit=crop",
                author: "Marcus Chen",
                authorTitle: "Senior Financial Analyst",
                authorImage: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face",
                date: "2024-12-15",
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
                id: 2,
                title: "Day Trading Strategies That Actually Work",
                category: "Trading",
                image: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&h=400&fit=crop",
                author: "Sarah Williams",
                authorTitle: "Professional Day Trader",
                authorImage: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face",
                date: "2024-12-12",
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

        // Initialize blog data
        function initializeBlogs() {
            if (!localStorage.getItem(BLOG_STORAGE_KEY)) {
                localStorage.setItem(BLOG_STORAGE_KEY, JSON.stringify(defaultBlogs));
            }
            return JSON.parse(localStorage.getItem(BLOG_STORAGE_KEY));
        }

        // Save blog data
        function saveBlogs(blogs) {
            localStorage.setItem(BLOG_STORAGE_KEY, JSON.stringify(blogs));
        }

        // Get all blogs
        function getAllBlogs() {
            return initializeBlogs();
        }

        // Get a specific blog
        function getBlog(id) {
            const blogs = getAllBlogs();
            return blogs[id];
        }

        // Add or update a blog
        function saveBlog(blogData) {
            const blogs = getAllBlogs();
            if (blogData.id) {
                // Update existing blog
                blogs[blogData.id] = blogData;
            } else {
                // Add new blog
                const newId = Math.max(...Object.keys(blogs).map(Number)) + 1;
                blogData.id = newId;
                blogs[newId] = blogData;
            }
            saveBlogs(blogs);
            return blogData.id;
        }

        // Delete a blog
        function deleteBlog(id) {
            const blogs = getAllBlogs();
            delete blogs[id];
            saveBlogs(blogs);
        }

        // Format date for display
        function formatDisplayDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Render blog list
        function renderBlogList() {
            const blogs = getAllBlogs();
            const blogContainer = document.getElementById('blog-container');
            blogContainer.innerHTML = '';
            
            // Separate featured and regular blogs
            const featuredBlogs = [];
            const regularBlogs = [];
            
            Object.values(blogs).forEach(blog => {
                if (blog.category === 'Featured') {
                    featuredBlogs.push(blog);
                } else {
                    regularBlogs.push(blog);
                }
            });
            
            // Render featured blogs first
            featuredBlogs.forEach(blog => {
                const blogElement = createFeaturedBlogElement(blog);
                blogContainer.appendChild(blogElement);
            });
            
            // Render regular blogs
            regularBlogs.forEach(blog => {
                const blogElement = createRegularBlogElement(blog);
                blogContainer.appendChild(blogElement);
            });
            
            // Render admin list
            renderAdminBlogList();
        }

        // Create featured blog element
        function createFeaturedBlogElement(blog) {
            const div = document.createElement('div');
            div.className = 'md:col-span-2 lg:col-span-3 blog-card bg-gradient-to-r from-primary to-secondary rounded-3xl overflow-hidden cursor-pointer';
            div.onclick = () => showBlogDetail(blog.id);
            
            div.innerHTML = `
                <div class="flex flex-col lg:flex-row">
                    <div class="lg:w-1/2 p-8 lg:p-12">
                        <div class="flex items-center mb-4">
                            <span class="bg-accent px-3 py-1 rounded-full text-sm font-semibold">${blog.category}</span>
                            <span class="ml-4 text-gray-300">${formatDisplayDate(blog.date)}</span>
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-bold mb-4">${blog.title}</h3>
                        <p class="text-lg text-gray-200 mb-6">${blog.content.substring(0, 100)}...</p>
                        <div class="flex items-center">
                            <img src="${blog.authorImage}" class="w-12 h-12 rounded-full mr-4" alt="${blog.author}">
                            <div>
                                <p class="font-semibold">${blog.author}</p>
                                <p class="text-gray-300 text-sm">${blog.authorTitle}</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2">
                        <img src="${blog.image}" class="w-full h-64 lg:h-full object-cover" alt="${blog.title}">
                    </div>
                </div>
            `;
            
            return div;
        }

        // Create regular blog element
        function createRegularBlogElement(blog) {
            const div = document.createElement('div');
            div.className = 'blog-card blog-card bg-gradient-to-r from-primary to-secondary rounded-2xl overflow-hidden cursor-pointer hover:bg-gray-750';
            div.onclick = () => showBlogDetail(blog.id);
            
            // Determine category color
            let categoryColor = 'bg-blue-600';
            if (blog.category === 'Budgeting') categoryColor = 'bg-green-600';
            if (blog.category === 'Investment') categoryColor = 'bg-purple-600';
            if (blog.category === 'Planning') categoryColor = 'bg-yellow-600';
            if (blog.category === 'Tax') categoryColor = 'bg-red-600';
            
            div.innerHTML = `
                <img src="${blog.image}" class="w-full h-48 object-cover" alt="${blog.title}">
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <span class="${categoryColor} px-3 py-1 rounded-full text-xs font-semibold">${blog.category}</span>
                           <span class="ml-4 text-gray-300">${formatDisplayDate(blog.date)}</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">${blog.title}</h3>
                       <p class="text-lg text-gray-200 mb-6">${blog.content.substring(0, 100)}...</p>
                    <div class="flex items-center">
                        <img src="${blog.authorImage}" class="w-8 h-8 rounded-full mr-3" alt="${blog.author}">
                        <span class="text-sm text-gray-300">${blog.author}</span>
                    </div>
                </div>
            `;
            
            return div;
        }

        // Show blog detail
        function showBlogDetail(blogId) {
            const blog = getBlog(blogId);
            if (!blog) return;

            document.getElementById('blog-image').src = blog.image;
            document.getElementById('blog-category').textContent = blog.category;
            document.getElementById('blog-title').textContent = blog.title;
            document.getElementById('author-image').src = blog.authorImage;
            document.getElementById('author-name').textContent = blog.author;
            document.getElementById('author-title').textContent = blog.authorTitle;
            document.getElementById('blog-date').textContent = formatDisplayDate(blog.date);
            document.getElementById('blog-content').innerHTML = blog.content;

            document.getElementById('blog-list').classList.add('hidden');
            document.getElementById('blog-detail').classList.remove('hidden');
            
            window.scrollTo(0, 0);
        }

        // Show blog list
        function showBlogList() {
            document.getElementById('blog-list').classList.remove('hidden');
            document.getElementById('blog-detail').classList.add('hidden');
        }

        // Render admin blog list
        function renderAdminBlogList() {
            const blogs = getAllBlogs();
            const adminList = document.getElementById('blog-list-admin');
            adminList.innerHTML = '';
            
            Object.values(blogs).forEach(blog => {
                const div = document.createElement('div');
                div.className = 'bg-gray-700 p-4 rounded-lg flex justify-between items-center';
                
                div.innerHTML = `
                    <div>
                        <h4 class="font-bold">${blog.title}</h4>
                        <p class="text-sm text-gray-400">${blog.category} • ${formatDisplayDate(blog.date)}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button class="edit-blog px-3 py-1 bg-blue-600 rounded hover:bg-blue-700" data-id="${blog.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-blog px-3 py-1 bg-red-600 rounded hover:bg-red-700" data-id="${blog.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                
                adminList.appendChild(div);
            });
            
            // Add event listeners to edit and delete buttons
            document.querySelectorAll('.edit-blog').forEach(button => {
                button.addEventListener('click', (e) => {
                    const id = e.target.closest('button').dataset.id;
                    editBlog(id);
                });
            });
            
            document.querySelectorAll('.delete-blog').forEach(button => {
                button.addEventListener('click', (e) => {
                    const id = e.target.closest('button').dataset.id;
                    deleteBlogAndRefresh(id);
                });
            });
        }

        // Edit blog
        function editBlog(id) {
            const blog = getBlog(id);
            if (!blog) return;
            
            document.getElementById('blog-id').value = blog.id;
            document.getElementById('blog-title-input').value = blog.title;
            document.getElementById('blog-category-input').value = blog.category;
            document.getElementById('blog-date-input').value = blog.date;
            document.getElementById('blog-image-input').value = blog.image;
            document.getElementById('blog-author-input').value = blog.author;
            document.getElementById('blog-author-title-input').value = blog.authorTitle;
            document.getElementById('blog-author-image-input').value = blog.authorImage;
            document.getElementById('blog-content-input').value = blog.content;
            
            document.getElementById('cancel-edit').classList.remove('hidden');
        }

        // Delete blog and refresh
        function deleteBlogAndRefresh(id) {
            if (confirm('Are you sure you want to delete this blog?')) {
                deleteBlog(id);
                renderBlogList();
            }
        }

        // Cancel edit
        function cancelEdit() {
            document.getElementById('blog-form').reset();
            document.getElementById('blog-id').value = '';
            document.getElementById('cancel-edit').classList.add('hidden');
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            // Render initial blog list
            renderBlogList();
            
            // Admin panel toggle
            document.getElementById('admin-toggle').addEventListener('click', () => {
                document.getElementById('admin-panel').style.display = 'block';
            });
            
            document.getElementById('close-admin').addEventListener('click', () => {
                document.getElementById('admin-panel').style.display = 'none';
            });
            
            // Blog form submission
            document.getElementById('blog-form').addEventListener('submit', (e) => {
                e.preventDefault();
                
                const blogData = {
                    id: document.getElementById('blog-id').value ? parseInt(document.getElementById('blog-id').value) : null,
                    title: document.getElementById('blog-title-input').value,
                    category: document.getElementById('blog-category-input').value,
                    date: document.getElementById('blog-date-input').value,
                    image: document.getElementById('blog-image-input').value,
                    author: document.getElementById('blog-author-input').value,
                    authorTitle: document.getElementById('blog-author-title-input').value,
                    authorImage: document.getElementById('blog-author-image-input').value,
                    content: document.getElementById('blog-content-input').value
                };
                
                saveBlog(blogData);
                renderBlogList();
                document.getElementById('blog-form').reset();
                document.getElementById('cancel-edit').classList.add('hidden');
                
                alert('Blog saved successfully!');
            });
            
            // Cancel edit button
            document.getElementById('cancel-edit').addEventListener('click', cancelEdit);
            
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

            const blogCards = document.querySelectorAll('.blog-card');
            blogCards.forEach(card => observer.observe(card));
        });
    </script>
</body>
@endsection