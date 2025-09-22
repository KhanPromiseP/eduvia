<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 sm:p-10">

            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <svg class="mx-auto w-14 h-14 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="mt-3 text-2xl font-bold text-gray-800">Create Account</h2>
                <p class="text-gray-500 text-sm">Join us today and get started</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="name" 
                        type="text" 
                        name="name" 
                        :value="old('name')" 
                        required autofocus autocomplete="name"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                    <x-text-input 
                        id="email" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required autocomplete="username"
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

                <!-- Preferred Language -->
                <div>
                    <x-input-label for="preferred_language" :value="__('Preferred Language')" class="text-gray-700 font-medium" />
                    <select id="preferred_language" name="preferred_language" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="en" {{ old('preferred_language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ old('preferred_language') == 'es' ? 'selected' : '' }}>Spanish</option>
                        <option value="fr" {{ old('preferred_language') == 'fr' ? 'selected' : '' }}>French</option>
                        <option value="de" {{ old('preferred_language') == 'de' ? 'selected' : '' }}>German</option>
                        <option value="it" {{ old('preferred_language') == 'it' ? 'selected' : '' }}>Italian</option>
                        <option value="pt" {{ old('preferred_language') == 'pt' ? 'selected' : '' }}>Portuguese</option>
                    </select>
                    <x-input-error :messages="$errors->get('preferred_language')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Learning Interests -->
                <div>
                    <x-input-label for="learning_interests" :value="__('Learning Interests (Optional)')" class="text-gray-700 font-medium" />
                    <div class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                        @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" name="learning_interests[]" value="{{ $category->id }}" 
                                    {{ in_array($category->id, old('learning_interests', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('learning_interests')" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Submit -->
                <div>
                    <x-primary-button class="w-full justify-center py-3 rounded-lg text-base">
                        {{ __('Register') }}
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
                Already have an account? 
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-800">Log in</a>
            </p>
        </div>
    </div>
</x-guest-layout>