<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Additional Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your additional profile information and learning preferences.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.additional.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Country -->
        <div>
            <x-input-label for="country" :value="__('Country')" />
            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" 
                :value="old('country', $user->country)" autocomplete="country" />
            <x-input-error class="mt-2" :messages="$errors->get('country')" />
        </div>

        <!-- City -->
        <div>
            <x-input-label for="city" :value="__('City')" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" 
                :value="old('city', $user->city)" autocomplete="address-level2" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <!-- Preferred Language -->
        <div>
            <x-input-label for="preferred_language" :value="__('Preferred Language')" />
            <select id="preferred_language" name="preferred_language" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="en" {{ old('preferred_language', $user->preferred_language) == 'en' ? 'selected' : '' }}>English</option>
                <option value="es" {{ old('preferred_language', $user->preferred_language) == 'es' ? 'selected' : '' }}>Spanish</option>
                <option value="fr" {{ old('preferred_language', $user->preferred_language) == 'fr' ? 'selected' : '' }}>French</option>
                <option value="de" {{ old('preferred_language', $user->preferred_language) == 'de' ? 'selected' : '' }}>German</option>
                <option value="it" {{ old('preferred_language', $user->preferred_language) == 'it' ? 'selected' : '' }}>Italian</option>
                <option value="pt" {{ old('preferred_language', $user->preferred_language) == 'pt' ? 'selected' : '' }}>Portuguese</option>
                <option value="ru" {{ old('preferred_language', $user->preferred_language) == 'ru' ? 'selected' : '' }}>Russian</option>
                <option value="zh" {{ old('preferred_language', $user->preferred_language) == 'zh' ? 'selected' : '' }}>Chinese</option>
                <option value="ja" {{ old('preferred_language', $user->preferred_language) == 'ja' ? 'selected' : '' }}>Japanese</option>
                <option value="ar" {{ old('preferred_language', $user->preferred_language) == 'ar' ? 'selected' : '' }}>Arabic</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('preferred_language')" />
        </div>

        <!-- Learning Interests -->
        <div>
            <x-input-label for="learning_interests" :value="__('Learning Interests')" />
            <div class="mt-2 space-y-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-3">
                @foreach($categories as $category)
                    <label class="flex items-center">
                        <input type="checkbox" name="learning_interests[]" value="{{ $category->id }}" 
                            {{ in_array($category->id, $user->categories->pluck('id')->toArray()) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('learning_interests')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'additional-info-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>