<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 sm:p-10">

            <!-- Header -->
            <div class="text-center mb-6">
                <svg class="mx-auto w-14 h-14 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="mt-3 text-2xl font-bold text-gray-800">Reset Password</h2>
                <p class="text-gray-500 text-sm">Enter your new password below</p>
            </div>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="email" 
                        type="email" 
                        name="email" 
                        :value="old('email', $request->email)" 
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
                        required autocomplete="new-password"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required autocomplete="new-password"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Submit -->
                <div>
                    <x-primary-button class="w-full justify-center py-3 rounded-lg text-base">
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Back to login -->
            <p class="mt-6 text-center text-sm text-gray-600">
                Remembered your password? 
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-800">Log in</a>
            </p>
        </div>
    </div>
</x-guest-layout>
