<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 sm:p-10">
            
            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <svg class="mx-auto w-14 h-14 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="mt-3 text-2xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-gray-500 text-sm">Log in to continue to your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="email" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required autofocus autocomplete="username"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required autocomplete="current-password"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center text-sm text-gray-600">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <div>
                    <x-primary-button class="w-full justify-center py-3 rounded-lg text-base">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center justify-center">
                <span class="h-px w-20 bg-gray-300"></span>
                <span class="mx-3 text-sm text-gray-500">or</span>
                <span class="h-px w-20 bg-gray-300"></span>
            </div>

            <!-- Extra Actions -->
            <p class="text-center text-sm text-gray-600">
                Don’t have an account? 
                <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:text-indigo-800">Sign up</a>
            </p>
        </div>
    </div>
</x-guest-layout>
