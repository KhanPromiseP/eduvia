<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Ad: {{ $ad->title }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" class="space-y-6 bg-white p-6 rounded shadow">
            @csrf
            @method('PUT')

            {{-- User --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">
                    Creator (User) <span class="text-red-500">*</span>
                </label>
                <select name="user_id" id="user_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ old('user_id', $ad->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Product --}}
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">
                    Related Product (optional)
                </label>
                <select name="product_id" id="product_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- None --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                            {{ old('product_id', $ad->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $ad->title) }}" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    required
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Select Type --</option>
                    @foreach(['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial'] as $type)
                        <option value="{{ $type }}" {{ old('type', $ad->type) == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">
                    Content (URL, JS code, or media path) <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="content" 
                    id="content" 
                    rows="3" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                >{{ old('content', $ad->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link --}}
            <div>
                <label for="link" class="block text-sm font-medium text-gray-700">
                    Link (optional)
                </label>
                <input 
                    type="url" 
                    name="link" 
                    id="link" 
                    value="{{ old('link', $ad->link) }}" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://example.com"
                >
                @error('link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start At --}}
            <div>
                <label for="start_at" class="block text-sm font-medium text-gray-700">
                    Start Date (optional)
                </label>
                <input 
                    type="date" 
                    name="start_at" 
                    id="start_at" 
                    value="{{ old('start_at', optional($ad->start_at)->format('Y-m-d')) }}" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                @error('start_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- End At --}}
            <div>
                <label for="end_at" class="block text-sm font-medium text-gray-700">
                    End Date (optional)
                </label>
                <input 
                    type="date" 
                    name="end_at" 
                    id="end_at" 
                    value="{{ old('end_at', optional($ad->end_at)->format('Y-m-d')) }}" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                @error('end_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Active --}}
            <div class="flex items-center space-x-2">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    id="is_active" 
                    value="1" 
                    {{ old('is_active', $ad->is_active) ? 'checked' : '' }}
                    class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                >
                <label for="is_active" class="block text-sm font-medium text-gray-700">Is Active?</label>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Placement --}}
            <div>
                <label for="placement" class="block text-sm font-medium text-gray-700">
                    Placement (optional)
                </label>
                <input 
                    type="text" 
                    name="placement" 
                    id="placement" 
                    value="{{ old('placement', $ad->placement) }}" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., header, sidebar, specific-page:/about"
                >
                @error('placement')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Targeting --}}
            <div>
                <label for="targeting" class="block text-sm font-medium text-gray-700">
                    Targeting (JSON format, optional)
                </label>
                <textarea 
                    name="targeting" 
                    id="targeting" 
                    rows="3" 
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder='{"devices": ["mobile"], "countries": ["US"], "locations": ["sitewide"]}'
                >{{ old('targeting', json_encode($ad->targeting)) }}</textarea>
                @error('targeting')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Random --}}
            <div class="flex items-center space-x-2">
                <input 
                    type="checkbox" 
                    name="is_random" 
                    id="is_random" 
                    value="1" 
                    {{ old('is_random', $ad->is_random) ? 'checked' : '' }}
                    class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                >
                <label for="is_random" class="block text-sm font-medium text-gray-700">Random Placement?</label>
                @error('is_random')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center space-x-4 pt-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="bi bi-save2 me-2"></i> Update Ad
                </button>

                <a href="{{ route('admin.ads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-arrow-left me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
