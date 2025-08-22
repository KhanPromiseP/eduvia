@extends('layouts.app')

@section('content')

<head>

    <title>Contact Us - Financial Excellence</title>
   
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
                            '0%, 100%': { boxShadow: '0 0 5px rgba(53, 114, 255, 0.5)' },
                            '50%': { boxShadow: '0 0 20px rgba(53, 181, 255, 0.8)' }
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
            border-color:rgb(53, 141, 255);
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
        
        .error-message-container {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
        }

        .success-message-container {
            background: linear-gradient(135deg, #10B981, #059669);
        }

        .message-slide-in {
            animation: slide-up 0.5s ease-out forwards;
        }

        @keyframes slide-up {
            0% { 
                transform: translateY(20px); 
                opacity: 0;
            }
            100% { 
                transform: translateY(0); 
                opacity: 1;
            }
        }
        
        .error-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-dark via-gray-900 to-primary text-white min-h-screen">
    

        <!-- Hero Section -->
        <section class="relative py-16 px-4 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-accent/20"></div>
            
            <div class="container mx-auto text-center relative z-10 max-w-4xl">
                <div class="animate-fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 text-gradient">
                        Get In Touch
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-700 mb-8 leading-relaxed">
                        Ready to transform your financial future? Let's discuss how our expert services can help you achieve your goals.
                    </p>
                    <div class="flex justify-center items-center space-x-8">
                        <i class="fas fa-comments floating-icon text-3xl text-gold"></i>
                        <i class="fas fa-handshake floating-icon text-3xl text-accent"></i>
                        <i class="fas fa-rocket floating-icon text-3xl text-secondary"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form & Info Section -->
        <section class="py-16 px-6">
            <div class="container mx-auto max-w-6xl">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    
                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="glass-effect rounded-3xl p-8 lg:p-12 animate-slide-up">
                            <h2 class="text-3xl font-bold mb-8 text-gradient flex items-center">
                                <i class="fas fa-envelope mr-4"></i>
                                Send Us a Message
                            </h2>
                            
                            <!-- Error Message -->
                            <div id="error-message" class="hidden error-message-container rounded-xl p-6 mb-8 message-slide-in">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-2xl mr-4"></i>
                                    <div class="flex-grow">
                                        <h3 class="font-bold text-lg">Error Sending Message</h3>
                                        <p id="error-message-text" class="text-red-100">Please check the form and try again.</p>
                                    </div>
                                    <button class="ml-4 text-white hover:text-gray-200 focus:outline-none" onclick="document.getElementById('error-message').classList.add('hidden')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Success Message -->
                            <div id="success-message" class="hidden success-message-container rounded-xl p-6 mb-8 message-slide-in">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-2xl mr-4"></i>
                                    <div class="flex-grow">
                                        <h3 class="font-bold text-lg">Message Sent Successfully!</h3>
                                        <p id="success-message-text" class="text-green-100">We'll get back to you within 24 hours.</p>
                                    </div>
                                    <button class="ml-4 text-white hover:text-gray-200 focus:outline-none" onclick="document.getElementById('success-message').classList.add('hidden')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <form id="contact-form" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="form-field">
                                        <label class="block text-sm font-semibold mb-2 text-gray-500">
                                            <i class="fas fa-user mr-2 text-accent"></i>Name*
                                        </label>
                                        <input 
                                            type="text" 
                                            name="firstName"
                                            required
                                            class="w-full px-4 py-3 bg-gray-100 border border-gray-600 rounded-xl text-gray-800 placeholder-gray-400 input-focus transition-all duration-300"
                                            placeholder="Enter your name"
                                        >
                                        <span class="error-message text-red-400 text-sm mt-1 hidden"></span>
                                    </div>

                                    <div class="form-field">
                                        <label class="block text-sm font-semibold mb-2 text-gray-500">
                                            <i class="fas fa-envelope mr-2 text-accent"></i>Email Address*
                                        </label>
                                        <input 
                                            type="email" 
                                            name="email"
                                            required
                                            class="w-full px-4 py-3 bg-gray-100 border border-gray-600 rounded-xl text-gray-800 placeholder-gray-400 input-focus transition-all duration-300"
                                            placeholder="your.email@example.com"
                                        >
                                        <span class="error-message text-red-400 text-sm mt-1 hidden"></span>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label class="block text-sm font-semibold mb-2 text-gray-500">
                                        <i class="fas fa-tags mr-2 text-accent"></i>Service Interest
                                    </label>
                                    <select 
                                        name="service"
                                        class="w-full px-4 py-3 bg-gray-100 border border-gray-600 rounded-xl text-gray-700 input-focus transition-all duration-300"
                                    >
                                        <option value="">Select a service...</option>
                                        <option value="consultation">Personal Consultation</option>
                                        <option value="portfolio">Portfolio Management</option>
                                        <option value="planning">Financial Planning</option>
                                        <option value="training">Investment Training</option>
                                        <option value="tax">Tax Optimization</option>
                                        <option value="digital">Digital Products</option>
                                        <option value="other">Others</option>
                                    </select>
                                </div>

                                <div class="form-field">
                                    <label class="block text-sm font-semibold mb-2 text-gray-500">
                                        <i class="fas fa-comment-dots mr-2 text-accent"></i>Message*
                                    </label>
                                    <textarea 
                                        name="message"
                                        required
                                        rows="5"
                                        class="w-full px-4 py-3 bg-gray-100 border border-gray-600 rounded-xl text-gray-800 placeholder-gray-400 input-focus transition-all duration-300 resize-none"
                                        placeholder="Tell us about your financial goals and how we can help you achieve them..."
                                    ></textarea>
                                    <span class="error-message text-red-400 text-sm mt-1 hidden"></span>
                                </div>

                                <div class="form-field">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="newsletter" class="mr-3 w-5 h-5 text-accent bg-gray-100 border-green-600 rounded focus:ring-accent">
                                        <span class="text-sm text-gray-500">
                                            Subscribe to our newsletter for financial tips and market insights
                                        </span>
                                    </label>
                                </div>

                                <button 
                                    type="submit" 
                                    id="submit-btn"
                                    class="w-full bg-blue-500 hover:bg-blue-600 py-4 rounded-xl font-bold text-lg transition-all duration-300 btn-glow transform hover:scale-105 flex items-center justify-center"
                                >
                                    <span id="btn-text">
                                        <i class="fas fa-paper-plane mr-3"></i>Send Message
                                    </span>
                                    <span id="btn-loading" class="hidden">
                                        <i class="fas fa-spinner animate-spin mr-3"></i>Sending...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <!-- Office Info -->
                        <div class="glass-effect rounded-3xl p-8 animate-slide-up">
                            <h3 class="text-2xl font-bold mb-6 text-gradient flex items-center">
                                <i class="fas fa-building mr-3"></i>
                                Information
                            </h3>
                            <div class="space-y-4">
                               
                                <div class="flex items-start">
                                    <i class="fas fa-phone text-gold mr-4 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-700">Phone</p>
                                        <p class="text-gray-500 text-sm">+(680) 854 767</p>
                                    </div>
                                </div>
                                <div class="flex items-start ">
                                    <i class="fas fa-envelope text-secondary mr-4 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-700">Email</p>
                                        <p class="text-gray-500 text-sm">khanpromise30@gmail.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-purple-400 mr-4 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-700">Business Hours</p>
                                        <p class="text-gray-500 text-sm">Mon-Fri: 9:00 AM - 6:00 PM <br>Sat: 10:00 AM - 2:00 PM </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Contact -->
                        <div class="glass-effect rounded-3xl p-8 animate-slide-up">
                            <h3 class="text-2xl font-bold mb-6 text-gradient flex items-center">
                                <i class="fas fa-bolt mr-3"></i>
                                Quick Reach Out
                            </h3>
                            <div class="space-y-4">
                            
                                <a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}" target="_blank" 
                                class="w-full bg-green-600 hover:bg-green-700 py-3 px-6 rounded-xl font-semibold transition-all flex items-center justify-center">
                                    <i class="fab fa-whatsapp mr-3"></i>
                                    WhatsApp Chat
                                </a>

                             
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="glass-effect rounded-3xl p-8 animate-slide-up">
                            <h3 class="text-2xl font-bold mb-6 text-gradient flex items-center">
                                <i class="fas fa-share-alt mr-3"></i>
                                Follow Us
                            </h3>
                            <div class="flex space-x-4">
                                <a href="#" class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition-all transform hover:scale-110">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center hover:bg-sky-600 transition-all transform hover:scale-110">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-blue-800 rounded-full flex items-center justify-center hover:bg-blue-900 transition-all transform hover:scale-110">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-pink-600 rounded-full flex items-center justify-center hover:bg-pink-700 transition-all transform hover:scale-110">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handler
    document.getElementById('contact-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');
        const successMessage = document.getElementById('success-message');
        const successMessageText = document.getElementById('success-message-text');
        const errorMessage = document.getElementById('error-message');
        const errorMessageText = document.getElementById('error-message-text');
        const formData = new FormData(form);
        
        // Hide all messages and clear errors
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.add('hidden');
        });
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        
        try {
            const response = await fetch('{{ route("contact.submit") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                // Handle validation errors
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorSpan = input.nextElementSibling;
                            errorSpan.textContent = messages[0];
                            errorSpan.classList.remove('hidden');
                            input.parentElement.classList.add('error-shake');
                            
                            setTimeout(() => {
                                input.parentElement.classList.remove('error-shake');
                            }, 500);
                        }
                    });
                    
                    // Show general error message
                    errorMessageText.textContent = 'Please correct the highlighted errors in the form.';
                    errorMessage.classList.remove('hidden');
                    errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    throw new Error(data.message || 'Form submission failed');
                }
            } else {
                // Success - update message with dynamic content
                successMessage.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-4"></i>
                        <div class="flex-grow">
                            <h3 class="font-bold text-lg">Message Sent Successfully!</h3>
                            <p>We've sent a confirmation email to <strong>${formData.get('email')}</strong> with helpful resources. 
                                <span class="text-red-500 text-sm">Check your inbox!</span>
                            </p>
                        </div>
                        <button class="ml-4 text-white hover:text-gray-200 focus:outline-none" onclick="document.getElementById('success-message').classList.add('hidden')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                successMessage.classList.add('message-slide-in');
                successMessage.classList.remove('hidden');
                form.reset();
                
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
        } catch (error) {
            console.error('Error:', error);
            
            // Show error message
            errorMessageText.textContent = error.message || 'An unexpected error occurred. Please try again later.';
            errorMessage.classList.remove('hidden');
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            
            // Hide messages after 8 seconds
            setTimeout(() => {
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
            }, 20000);
        }
    });

    // Clear field errors when user starts typing
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', function() {
            const errorSpan = this.nextElementSibling;
            if (errorSpan && errorSpan.classList.contains('error-message')) {
                errorSpan.classList.add('hidden');
            }
        });
        
        // Focus effects
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });

    // Animation observer for elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = Math.random() * 0.3 + 's';
                entry.target.classList.add('opacity-100');
            }
        });
    }, observerOptions);

    // Animate elements on load
    const animatedElements = document.querySelectorAll('.animate-slide-up, .animate-fade-in');
    animatedElements.forEach(el => {
        el.classList.add('opacity-0');
        observer.observe(el);
    });
});
</script>
</body>
@endsection

