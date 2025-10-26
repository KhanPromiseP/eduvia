<div id="global-profile-modal" x-data="globalProfileModal()" x-show="isOpen" x-cloak 
     class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         @click="close">
    </div>

    <!-- Modal -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            
            <!-- Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-indigo-200" 
                                 src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                                 alt="{{ Auth::user()->name }}">
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                {{ Auth::user()->name }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ __('Manage your profile and preferences') }}
                            </p>
                        </div>
                    </div>
                    <button @click="close" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-gray-50 px-6 py-6" id="profile-modal-content">
                <!-- Content will be loaded here via AJAX -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Loading profile...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function globalProfileModal() {
    return {
        isOpen: false,
        currentSection: null,
        
        open(section = null) {
            this.isOpen = true;
            this.currentSection = section;
            document.body.classList.add('overflow-hidden');
            
            // Load content
            this.loadProfileContent(section);
            
            // Escape key handler
            this.escapeHandler = (e) => {
                if (e.key === 'Escape') this.close();
            };
            document.addEventListener('keydown', this.escapeHandler);
        },
        
        close() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
            document.removeEventListener('keydown', this.escapeHandler);
        },
        
        async loadProfileContent(section = null) {
            const contentDiv = document.getElementById('profile-modal-content');
            
            try {
                let url = '{{ route("profile.modal") }}';
                if (section) {
                    url += `?section=${section}`;
                }
                
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const html = await response.text();
                contentDiv.innerHTML = html;
                
                // Initialize Alpine on new content
                if (typeof Alpine !== 'undefined') {
                    Alpine.initTree(contentDiv);
                }
                
                // Attach form handlers
                this.attachFormHandlers();
                
            } catch (error) {
                console.error('Error loading profile content:', error);
                contentDiv.innerHTML = `
                    <div class="text-center py-12">
                        <div class="text-red-500 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to Load</h3>
                        <p class="text-gray-500 mb-4">Unable to load profile editor.</p>
                        <button onclick="location.reload()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Try Again
                        </button>
                    </div>
                `;
            }
        },
        
        attachFormHandlers() {
            // Intercept all form submissions within the modal
            const forms = document.querySelectorAll('#profile-modal-content form');
            forms.forEach(form => {
                form.addEventListener('submit', this.handleFormSubmit.bind(this));
            });
        },
        
        async handleFormSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Saving...
                </div>
            `;
            
            try {
                const formData = new FormData(form);
                const url = form.getAttribute('action') || form.action;
                const method = form.getAttribute('method') || 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Success - show message and reload the section
                    this.showSuccessMessage(data.message || 'Profile updated successfully!');
                    
                    // Reload the content to reflect changes
                    setTimeout(() => {
                        this.loadProfileContent(this.currentSection);
                    }, 1500);
                    
                } else {
                    // Validation errors
                    this.showErrors(data.errors || {});
                }
                
            } catch (error) {
                console.error('Form submission error:', error);
                this.showErrorMessage('An error occurred while saving. Please try again.');
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        },
        
        showSuccessMessage(message) {
            this.showMessage(message, 'green');
        },
        
        showErrorMessage(message) {
            this.showMessage(message, 'red');
        },
        
        showMessage(message, color = 'green') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 z-[101] p-4 rounded-lg shadow-lg bg-${color}-50 border border-${color}-200 text-${color}-800`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        },
        
        showErrors(errors) {
            // Clear previous errors
            this.clearErrors();
            
            // Show new errors
            Object.keys(errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.innerHTML = errors[field].join('<br>');
                    input.parentNode.appendChild(errorDiv);
                    input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                }
            });
        },
        
        clearErrors() {
            document.querySelectorAll('.text-red-600').forEach(el => el.remove());
            document.querySelectorAll('.border-red-300').forEach(el => {
                el.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
            });
        }
    }
}

// Global function to open profile modal
window.openProfileModal = function(section = null) {
    const modal = document.querySelector('#global-profile-modal');
    if (modal) {
        const modalComponent = Alpine.$data(modal);
        modalComponent.open(section);
    }
};

// Initialize when Alpine is ready
document.addEventListener('alpine:init', function() {
    console.log('Global profile modal initialized');
});
</script>