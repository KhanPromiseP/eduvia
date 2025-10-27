@extends('layouts.admin')

@section('content')

   <style>
    :root {
        --navbar-height: 72px;
    }
    
    .doc-section {
        line-height: 1.7;
        margin-bottom: 4rem;
        scroll-margin-top: 120px;
    }
    
    .doc-section h2 {
        font-size: 1.875rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 1.5rem;
        margin-top: 3rem;
        border-bottom: 3px solid #e5e7eb;
        padding-bottom: 0.75rem;
    }
    
    .doc-section h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        margin-top: 2rem;
    }
    
    .doc-section p {
        margin-bottom: 1.25rem;
        color: #4b5563;
        line-height: 1.8;
    }
    
    .nav-item.active {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        color: white !important;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .nav-item.active i:first-child {
        color: white !important;
    }
    
    /* Smooth transitions */
    .nav-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Custom scrollbar for sidebar */
    #doc-nav::-webkit-scrollbar {
        width: 6px;
    }
    
    #doc-nav::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    #doc-nav::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #doc-nav::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Collapsible sections */
    .collapsible-section > div:first-child {
        cursor: pointer;
    }
    
    .collapsible-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .collapsible-content.open {
        max-height: 2000px;
    }
    
    .collapsible-arrow {
        transition: transform 0.3s ease;
    }
    
    .collapsible-section.open .collapsible-arrow {
        transform: rotate(90deg);
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .doc-section {
            break-inside: avoid;
        }
    }

    /* FIXED SIDEBAR STICKY STYLES - GUARANTEED TO WORK */
    .sidebar-container {
        position: relative;
        height: fit-content;
    }

    /* Desktop Sticky Behavior */
    @media (min-width: 1024px) {
        .sidebar {
            position: sticky;
            top: 32px; /* 20px + navbar offset */
            height: fit-content;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            z-index: 40;
        }

        .sidebar-scroll {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
        }

        /* Ensure parent containers allow sticky */
        .flex-col.lg\:flex-row {
            align-items: flex-start;
            position: relative;
        }
    }

    /* Mobile Behavior */
    @media (max-width: 1023px) {
        .sidebar {
            position: fixed;
            transform: translateX(-100%);
            top: var(--navbar-height);
            left: 0;
            height: calc(100vh - var(--navbar-height));
            width: 320px;
            overflow-y: auto;
            z-index: 50;
        }
        
        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-scroll {
            max-height: calc(100vh - var(--navbar-height) - 120px);
            overflow-y: auto;
        }
    }

    /* Safari compatibility */
    @supports (position: -webkit-sticky) {
        @media (min-width: 1024px) {
            .sidebar {
                position: -webkit-sticky;
            }
        }
    }

    /* Sidebar overlay for mobile */
    .sidebar-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 30;
    }

    /* Ensure main content area has proper positioning */
    .main-content {
        position: relative;
        z-index: 10;
    }

    /* Make sure the main container has enough height */
    .min-h-screen {
        min-height: 100vh;
        position: relative;
    }


    /* Add this to your existing CSS */
.min-h-screen {
    position: relative;
    min-height: 100vh;
}

.flex-col.lg\:flex-row {
    position: relative;
    align-items: flex-start;
}

.sidebar-container {
    position: relative;
    height: fit-content;
}

/* Enhanced sticky behavior with better browser support */
@media (min-width: 1024px) {
    .sidebar {
        position: sticky;
        top: 100px; /* Increased from 32px for better spacing */
        height: fit-content;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
        z-index: 40;
    }
    
    /* Ensure the parent flex container has proper alignment */
    .flex-col.lg\:flex-row {
        align-items: flex-start;
    }
}

/* Add this for additional browser support */
@supports (position: sticky) or (position: -webkit-sticky) {
    @media (min-width: 1024px) {
        .sidebar {
            position: -webkit-sticky;
            position: sticky;
        }
    }
}
</style>

    <!-- Navbar (simulated) -->
    <nav class="left-5  fixed top-[70px]">
        <div class="flex justify-between items-center">
        
            <div class="flex items-center space-x-4">
                <button class="lg:hidden text-sm text-blue-600 border border-blue-200 rounded z-50" id="sidebar-toggle">
                    Docs Menu
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 pt-[var(--navbar-height)]">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-graduation-cap text-indigo-600 mr-3"></i>
                    Eduvia Instructor Documentation
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Complete guide to creating, managing, and optimizing your courses on Eduvia
                </p>
                <div class="mt-6 flex justify-center space-x-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        <span class="text-sm font-medium">Last Updated: Oct 15, 2023</span>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                        <i class="fas fa-book text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium">24 Comprehensive Sections</span>
                    </div>
                </div>
            </div>

          <div class="flex flex-col lg:flex-row gap-8 items-start">
    <!-- Enhanced Sidebar with Sticky Behavior -->
    <div class="lg:w-80 flex-shrink-0 sidebar-container">
        <div class="sidebar bg-white rounded-2xl shadow-lg border border-gray-200">
                    <!-- Search Section -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="relative">
                            <input type="text" 
                                placeholder="Search documentation..." 
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                id="doc-search">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav id="doc-nav" class="p-4 sidebar-scroll">
                        <div class="space-y-1">
                                <!-- Getting Started Section -->
                                <div class="mb-4 collapsible-section">
                                    <div class="flex items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg mb-2 cursor-pointer">
                                        <i class="fas fa-rocket text-green-600 mr-3 w-5"></i>
                                        <span class="font-semibold text-gray-900 flex-grow">Getting Started</span>
                                        <i class="fas fa-chevron-right text-gray-500 text-sm collapsible-arrow"></i>
                                    </div>
                                    <div class="collapsible-content ml-4">
                                        <button data-target="account-setup" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-green-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-user-cog text-green-500 mr-3 group-hover:text-green-600 transition-colors"></i>
                                            <span>Account Setup</span>
                                        </button>
                                        <button data-target="platform-overview" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-green-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-compass text-green-500 mr-3 group-hover:text-green-600 transition-colors"></i>
                                            <span>Platform Overview</span>
                                        </button>
                                        <button data-target="first-course" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-green-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-play text-green-500 mr-3 group-hover:text-green-600 transition-colors"></i>
                                            <span>Creating First Course</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Course Creation Section -->
                                <div class="mb-4 collapsible-section">
                                    <div class="flex items-center p-3 bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg mb-2 cursor-pointer">
                                        <i class="fas fa-plus-circle text-purple-600 mr-3 w-5"></i>
                                        <span class="font-semibold text-gray-900 flex-grow">Course Creation</span>
                                        <i class="fas fa-chevron-right text-gray-500 text-sm collapsible-arrow"></i>
                                    </div>
                                    <div class="collapsible-content ml-4">
                                        <button data-target="course-basics" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-purple-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-info-circle text-purple-500 mr-3 group-hover:text-purple-600 transition-colors"></i>
                                            <span>Course Basics</span>
                                        </button>
                                        <button data-target="pricing-strategy" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-purple-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-tag text-purple-500 mr-3 group-hover:text-purple-600 transition-colors"></i>
                                            <span>Pricing Strategy</span>
                                        </button>
                                        <button data-target="course-structure" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-purple-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-sitemap text-purple-500 mr-3 group-hover:text-purple-600 transition-colors"></i>
                                            <span>Course Structure</span>
                                        </button>
                                        <button data-target="module-planning" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-purple-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-layer-group text-purple-500 mr-3 group-hover:text-purple-600 transition-colors"></i>
                                            <span>Module Planning</span>
                                        </button>
                                        <button data-target="content-preparation" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-purple-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-clipboard-list text-purple-500 mr-3 group-hover:text-purple-600 transition-colors"></i>
                                            <span>Content Preparation</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Content Management Section -->
                                <div class="mb-4 collapsible-section">
                                    <div class="flex items-center p-3 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg mb-2 cursor-pointer">
                                        <i class="fas fa-file-video text-orange-600 mr-3 w-5"></i>
                                        <span class="font-semibold text-gray-900 flex-grow">Content Management</span>
                                        <i class="fas fa-chevron-right text-gray-500 text-sm collapsible-arrow"></i>
                                    </div>
                                    <div class="collapsible-content ml-4">
                                        <button data-target="video-uploads" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-orange-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-video text-orange-500 mr-3 group-hover:text-orange-600 transition-colors"></i>
                                            <span>Video Uploads</span>
                                        </button>
                                        <button data-target="document-uploads" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-orange-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-file-pdf text-orange-500 mr-3 group-hover:text-orange-600 transition-colors"></i>
                                            <span>Document Uploads</span>
                                        </button>
                                        <button data-target="external-content" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-orange-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fab fa-youtube text-orange-500 mr-3 group-hover:text-orange-600 transition-colors"></i>
                                            <span>External Content</span>
                                        </button>
                                        <button data-target="file-specifications" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-orange-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-cogs text-orange-500 mr-3 group-hover:text-orange-600 transition-colors"></i>
                                            <span>File Specifications</span>
                                        </button>
                                        <button data-target="storage-management" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-orange-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-database text-orange-500 mr-3 group-hover:text-orange-600 transition-colors"></i>
                                            <span>Storage Management</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Security & Protection Section -->
                                <div class="mb-4 collapsible-section">
                                    <div class="flex items-center p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg mb-2 cursor-pointer">
                                        <i class="fas fa-shield-alt text-red-600 mr-3 w-5"></i>
                                        <span class="font-semibold text-gray-900 flex-grow">Security & Protection</span>
                                        <i class="fas fa-chevron-right text-gray-500 text-sm collapsible-arrow"></i>
                                    </div>
                                    <div class="collapsible-content ml-4">
                                        <button data-target="drm-protection" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-red-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-lock text-red-500 mr-3 group-hover:text-red-600 transition-colors"></i>
                                            <span>DRM Protection</span>
                                        </button>
                                        <button data-target="download-controls" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-red-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-download text-red-500 mr-3 group-hover:text-red-600 transition-colors"></i>
                                            <span>Download Controls</span>
                                        </button>
                                        <button data-target="access-controls" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-red-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-user-shield text-red-500 mr-3 group-hover:text-red-600 transition-colors"></i>
                                            <span>Access Controls</span>
                                        </button>
                                        <button data-target="content-security" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-red-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-fingerprint text-red-500 mr-3 group-hover:text-red-600 transition-colors"></i>
                                            <span>Content Security</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Best Practices Section -->
                                <div class="mb-4 collapsible-section">
                                    <div class="flex items-center p-3 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg mb-2 cursor-pointer">
                                        <i class="fas fa-star text-yellow-600 mr-3 w-5"></i>
                                        <span class="font-semibold text-gray-900 flex-grow">Best Practices</span>
                                        <i class="fas fa-chevron-right text-gray-500 text-sm collapsible-arrow"></i>
                                    </div>
                                    <div class="collapsible-content ml-4">
                                        <button data-target="engagement-tips" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-yellow-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-bullseye text-yellow-500 mr-3 group-hover:text-yellow-600 transition-colors"></i>
                                            <span>Student Engagement</span>
                                        </button>
                                        <button data-target="quality-standards" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-yellow-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-award text-yellow-500 mr-3 group-hover:text-yellow-600 transition-colors"></i>
                                            <span>Quality Standards</span>
                                        </button>
                                        <button data-target="course-optimization" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-yellow-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-chart-line text-yellow-500 mr-3 group-hover:text-yellow-600 transition-colors"></i>
                                            <span>Course Optimization</span>
                                        </button>
                                        <button data-target="marketing-tips" class="w-full text-left flex items-center py-2 px-3 text-sm rounded-lg hover:bg-yellow-50 text-gray-700 transition-colors nav-item group">
                                            <i class="fas fa-megaphone text-yellow-500 mr-3 group-hover:text-yellow-600 transition-colors"></i>
                                            <span>Marketing & Promotion</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-8 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-200">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-bolt text-blue-600 mr-2"></i>
                                        Quick Actions
                                    </h4>
                                    <div class="space-y-2">
                                        <a href="#" 
                                           class="flex items-center p-3 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors text-sm font-medium border border-blue-200 shadow-sm">
                                            <i class="fas fa-plus mr-2 text-blue-600"></i>
                                            Create New Course
                                        </a>
                                        <a href="#" 
                                           class="flex items-center p-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium border border-gray-200 shadow-sm">
                                            <i class="fas fa-cog mr-2 text-gray-600"></i>
                                            Manage Courses
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="flex-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                        <!-- Content Header -->
                        <div class="border-b border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 id="current-section" class="text-2xl font-bold text-gray-900">Getting Started</h2>
                                    <p id="section-description" class="text-gray-600 mt-1">Complete guide to setting up your instructor account</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button onclick="printSection()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center text-sm font-medium">
                                        <i class="fas fa-print mr-2"></i>
                                        Print
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Documentation Content -->
                        <div class="p-8 max-w-4xl mx-auto documentation-content" id="doc-content">
                            <!-- All content sections in one container for better navigation -->
                            <div id="content-container">
                                <!-- Account Setup Section -->
                                <div id="account-setup" class="doc-section">
                                    <h2>Account Setup</h2>
                                    <p class="section-description">Learn how to set up your instructor account for success on Eduvia.</p>
                                    
                                    <h3>Creating Your Account</h3>
                                    <p>To get started as an instructor on Eduvia, you'll first need to create an account. Visit our signup page and select the "Instructor" option during registration.</p>
                                    
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-lightbulb text-blue-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700">
                                                    <strong>Pro Tip:</strong> Use a professional email address that you check regularly for instructor communications.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h3>Profile Completion</h3>
                                    <p>After creating your account, complete your instructor profile with the following information:</p>
                                    <ul class="list-disc pl-5 my-4 space-y-2">
                                        <li>Professional bio highlighting your expertise</li>
                                        <li>High-quality profile picture</li>
                                        <li>Links to your website or social media</li>
                                        <li>Areas of specialization</li>
                                    </ul>
                                    
                                    <h3>Verification Process</h3>
                                    <p>To ensure quality and security, Eduvia requires all instructors to complete a verification process:</p>
                                    <ol class="list-decimal pl-5 my-4 space-y-2">
                                        <li>Email verification</li>
                                        <li>Identity confirmation</li>
                                        <li>Expertise validation</li>
                                        <li>Payment setup</li>
                                    </ol>
                                    
                                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 my-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    <strong>Note:</strong> The verification process typically takes 1-2 business days. You can start creating course content while waiting for approval.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Platform Overview Section -->
                                <div id="platform-overview" class="doc-section">
                                    <h2>Platform Overview</h2>
                                    <p class="section-description">Familiarize yourself with the Eduvia instructor dashboard and tools.</p>
                                    
                                    <h3>Instructor Dashboard</h3>
                                    <p>Your instructor dashboard is the central hub for managing all your courses, students, and earnings. Key sections include:</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-chart-line text-indigo-600"></i>
                                                </div>
                                                <h4 class="font-semibold">Analytics</h4>
                                            </div>
                                            <p class="text-sm text-gray-600">Track course performance, student engagement, and revenue metrics.</p>
                                        </div>
                                        
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-users text-green-600"></i>
                                                </div>
                                                <h4 class="font-semibold">Students</h4>
                                            </div>
                                            <p class="text-sm text-gray-600">Manage student enrollments, communications, and progress tracking.</p>
                                        </div>
                                        
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-book text-purple-600"></i>
                                                </div>
                                                <h4 class="font-semibold">Courses</h4>
                                            </div>
                                            <p class="text-sm text-gray-600">Create, edit, and organize your course content and curriculum.</p>
                                        </div>
                                        
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-comments text-yellow-600"></i>
                                                </div>
                                                <h4 class="font-semibold">Communication</h4>
                                            </div>
                                            <p class="text-sm text-gray-600">Interact with students through announcements, discussions, and Q&A.</p>
                                        </div>
                                    </div>
                                    
                                    <h3>Navigation Tips</h3>
                                    <p>Use the sidebar navigation to quickly access different sections of your instructor dashboard. The mobile app provides the same functionality with a touch-optimized interface.</p>
                                </div>

                                <!-- First Course Section -->
                                <div id="first-course" class="doc-section">
                                    <h2>Creating Your First Course</h2>
                                    <p class="section-description">Step-by-step guide to planning, creating, and publishing your first course on Eduvia.</p>
                                    
                                    <h3>Course Planning</h3>
                                    <p>Before creating your course, it's important to plan effectively:</p>
                                    <ul class="list-disc pl-5 my-4 space-y-2">
                                        <li>Define your target audience and their learning goals</li>
                                        <li>Outline the course structure and learning objectives</li>
                                        <li>Plan your content delivery method (video, text, quizzes, etc.)</li>
                                        <li>Estimate the total course duration</li>
                                    </ul>
                                    
                                    <h3>Content Creation</h3>
                                    <p>Create engaging content that delivers value to your students:</p>
                                    <div class="bg-green-50 border-l-4 border-green-500 p-4 my-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-check-circle text-green-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-green-700">
                                                    <strong>Best Practice:</strong> Break down complex topics into digestible modules of 5-15 minutes each for better student retention.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h3>Course Publishing</h3>
                                    <p>Once your course content is ready, follow these steps to publish:</p>
                                    <ol class="list-decimal pl-5 my-4 space-y-2">
                                        <li>Add course title, subtitle, and description</li>
                                        <li>Upload course image and promotional video</li>
                                        <li>Set pricing and enrollment options</li>
                                        <li>Submit for quality review</li>
                                        <li>Publish and promote your course</li>
                                    </ol>
                                </div>

                                <!-- Course Basics Section -->
                                <div id="course-basics" class="doc-section">
                                    <h2>Course Basics</h2>
                                    <p class="section-description">Understanding the fundamental elements of a successful course on Eduvia.</p>
                                    
                                    <h3>Course Structure</h3>
                                    <p>Every Eduvia course consists of the following components:</p>
                                    <ul class="list-disc pl-5 my-4 space-y-2">
                                        <li><strong>Course Landing Page:</strong> Your course's storefront with key information</li>
                                        <li><strong>Curriculum:</strong> Organized sections and lectures</li>
                                        <li><strong>Resources:</strong> Downloadable materials and supplementary content</li>
                                        <li><strong>Q&A Section:</strong> Interactive space for student questions</li>
                                        <li><strong>Announcements:</strong> Communication channel with enrolled students</li>
                                    </ul>
                                    
                                    <h3>Content Requirements</h3>
                                    <p>To ensure quality, all Eduvia courses must meet these requirements:</p>
                                    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 my-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-circle text-orange-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-orange-700">
                                                    <strong>Important:</strong> All video content must be at least 720p HD quality, and courses must have a minimum of 30 minutes of video content.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Video Uploads Section -->
                                <div id="video-uploads" class="doc-section">
                                    <h2>Video Uploads</h2>
                                    <p class="section-description">Best practices for uploading and optimizing video content on Eduvia.</p>
                                    
                                    <h3>Supported Formats</h3>
                                    <p>Eduvia supports the following video formats for course content:</p>
                                    <ul class="list-disc pl-5 my-4 space-y-2">
                                        <li>MP4 (recommended)</li>
                                        <li>MOV</li>
                                        <li>AVI</li>
                                        <li>WMV</li>
                                        <li>FLV</li>
                                    </ul>
                                    
                                    <h3>Video Specifications</h3>
                                    <p>For optimal playback quality, follow these specifications:</p>
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700">
                                                    <strong>Recommended Settings:</strong> 1280x720 resolution (720p) or higher, H.264 codec, 30 fps, and AAC audio at 192 kbps.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" id="sidebar-overlay" style="display: none;"></div>

    <script>
        let currentSection = 'account-setup';

        // Initialize navigation
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            setupSearch();
            setupQuickNav();
            showSection('account-setup');
            
            // Set navbar height dynamically
            const navbar = document.querySelector("nav");
            if (navbar) {
                document.documentElement.style.setProperty("--navbar-height", navbar.offsetHeight + "px");
            }
        });

        function initializeNavigation() {
            // Handle navigation item clicks
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSection = this.getAttribute('data-target');
                    if (targetSection) {
                        showSection(targetSection);
                        
                        // Update active states
                        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Close sidebar on mobile after selection
                        if (window.innerWidth < 1024) {
                            closeSidebar();
                        }
                    }
                });
            });

            // Auto-activate based on scroll
            setupScrollSpy();
            
            // Setup collapsible sections
            setupCollapsibleSections();
            
            // Setup sidebar toggle
            setupSidebarToggle();
        }

        function showSection(sectionId) {
            currentSection = sectionId;
            
            // Scroll to the specific subsection
            const subsection = document.getElementById(sectionId);
            if (subsection) {
                // Update header
                const sectionTitle = subsection.querySelector('h2')?.textContent || 
                                   sectionId.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                document.getElementById('current-section').textContent = sectionTitle;
                
                // Update description
                const description = subsection.querySelector('.section-description')?.textContent || 
                                   'Comprehensive guide and best practices';
                document.getElementById('section-description').textContent = description;
                
                // Smooth scroll to subsection with offset for navbar
                const navbarHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--navbar-height')) || 72;
                const elementPosition = subsection.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - navbarHeight - 20;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Update active nav item
                updateActiveNavItem(sectionId);
            }
        }

        function updateActiveNavItem(sectionId) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-target') === sectionId) {
                    item.classList.add('active');
                }
            });
        }

        function setupSearch() {
            const searchInput = document.getElementById('doc-search');
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                
                if (query.length === 0) {
                    // Show all items when search is cleared
                    document.querySelectorAll('.nav-item').forEach(item => {
                        item.style.display = 'flex';
                    });
                    document.querySelectorAll('.collapsible-section').forEach(section => {
                        section.style.display = 'block';
                    });
                    return;
                }
                
                let foundAny = false;
                document.querySelectorAll('.nav-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.display = 'flex';
                        foundAny = true;
                        
                        // Ensure parent sections are visible and expanded
                        let parentSection = item.closest('.collapsible-section');
                        while (parentSection) {
                            parentSection.style.display = 'block';
                            const content = parentSection.querySelector('.collapsible-content');
                            if (content && !content.classList.contains('open')) {
                                parentSection.classList.add('open');
                                content.classList.add('open');
                            }
                            parentSection = parentSection.parentElement.closest('.collapsible-section');
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Hide sections with no visible items
                document.querySelectorAll('.collapsible-section').forEach(section => {
                    const visibleItems = section.querySelectorAll('.nav-item[style="display: flex"]');
                    if (visibleItems.length === 0) {
                        section.style.display = 'none';
                    }
                });
            });
        }

        function setupScrollSpy() {
            const sections = document.querySelectorAll('.doc-section h2');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.parentElement.id;
                        if (sectionId && sectionId !== currentSection) {
                            currentSection = sectionId;
                            updateActiveNavItem(sectionId);
                            
                            // Update header
                            const sectionTitle = entry.target.textContent;
                            document.getElementById('current-section').textContent = sectionTitle;
                        }
                    }
                });
            }, { 
                rootMargin: '-20% 0px -60% 0px',
                threshold: 0.1
            });

            sections.forEach(section => {
                observer.observe(section);
            });
        }

        function setupQuickNav() {
            // Add keyboard shortcut
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'k') {
                    e.preventDefault();
                    document.getElementById('doc-search').focus();
                }
            });
        }

        function printSection() {
            window.print();
        }

        function setupCollapsibleSections() {
            // Initially all sections are closed
            document.querySelectorAll('.collapsible-section').forEach(section => {
                section.classList.remove('open');
                const content = section.querySelector('.collapsible-content');
                if (content) {
                    content.classList.remove('open');
                }
            });

            document.querySelectorAll('.collapsible-section > div:first-child').forEach(header => {
                header.addEventListener('click', function() {
                    const section = this.parentElement;
                    const content = section.querySelector('.collapsible-content');
                    
                    section.classList.toggle('open');
                    content.classList.toggle('open');
                });
            });
        }

        function setupSidebarToggle() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.add('open');
                sidebarOverlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
            
            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });
            
            // Close sidebar when clicking on empty space on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024 && 
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) &&
                    sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });
        }

        function closeSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.remove('open');
            sidebarOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Enhanced search with debouncing
        let searchTimeout;
        document.getElementById('doc-search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                setupSearch();
            }, 300);
        });

        // Initialize first section as active
        document.querySelector('[data-target="account-setup"]').classList.add('active');
    </script>

@endsection