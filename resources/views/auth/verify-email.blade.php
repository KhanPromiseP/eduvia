<x-guest-layout>
    <div 
        class="mb-4 text-sm text-gray-600 transform transition-transform duration-700 translate-x-[-100%] animate-slide-in"
    >
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div 
            class="mb-4 font-medium text-sm text-green-600 transform transition-transform duration-700 translate-x-[-100%] animate-slide-in"
        >
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>

        <!-- New Block button -->
        <a href="{{ url('./blog.index') }}" 
            class="ml-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            Block
        </a>
    </div>

    <!-- Add Tailwind keyframes for sliding in -->
    <style>
        @keyframes slide-in {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in {
            animation: slide-in 0.7s ease-out forwards;
        }
    </style>
</x-guest-layout>
