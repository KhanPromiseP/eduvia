<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}"
          class="animate__animated animate__slideInRight">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Buttons -->
        <div class="flex justify-between mt-4">
            <!-- Confirm -->
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>

            <!-- Block button -->
            <a href="{{ route('blog.index') }}"
               class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                {{ __('Block') }}
            </a>
        </div>
    </form>
</x-guest-layout>
