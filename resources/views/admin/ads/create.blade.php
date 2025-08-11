<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Create New Ad
        </h2>
    </x-slot>

    {{-- Alpine.js for live preview --}}
    <div x-data="adForm()" class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        <form
            action="{{ route('admin.ads.store') }}"
            method="POST"
            class="space-y-6 bg-white p-6 rounded shadow"
            @submit.prevent="submitForm"
        >
            @csrf

            {{-- User --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">
                    Creator (User) <span class="text-red-500">*</span>
                </label>
                <select
                    name="user_id"
                    id="user_id"
                    x-model="form.user_id"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Select the user who will be credited as the ad creator.
                </p>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Product --}}
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">
                    Related Product (optional)
                </label>
                <select
                    name="product_id"
                    id="product_id"
                    x-model="form.product_id"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">-- None --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Associate this ad with a product if relevant (optional).
                </p>
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
                    x-model="form.title"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Summer Sale Banner"
                    required
                >
                <p class="mt-1 text-xs text-gray-500">
                    Give your ad a clear, descriptive title (max 255 characters).
                </p>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">
                    Type <span class="text-red-500">*</span>
                </label>
                <select
                    name="type"
                    id="type"
                    x-model="form.type"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Select Type --</option>
                    @foreach(['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial'] as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Choose how the ad will be displayed.  
                    <br>
                    <strong>Image:</strong> A static image (recommended size 728x90 or 300x250 px).  
                    <br>
                    <strong>Video:</strong> MP4 video URL or path.  
                    <br>
                    <strong>Banner:</strong> Custom HTML or image banner.  
                    <br>
                    <strong>JS:</strong> JavaScript code snippet for advanced ads.  
                    <br>
                    <strong>Popup/Interstitial:</strong> Modal ads that overlay the page.
                </p>
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
                    x-model="form.content"
                    rows="4"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="For images/videos: enter the full URL or relative path.  
For JS: enter your JavaScript code here.  
For banner: paste your HTML content."
                    required
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 whitespace-pre-line">
                    Examples:  
                    - Image: https://example.com/banner.jpg  
                    - Video: https://example.com/video.mp4  
                    - JS: &lt;script&gt;alert('Hello')&lt;/script&gt;  
                    - Banner: &lt;div style='background:#eee;padding:10px;'&gt;Your Banner&lt;/div&gt;
                </p>
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
                    x-model="form.link"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://example.com"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Optional: Add a target URL so users click through the ad.
                </p>
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
                    x-model="form.start_at"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Optional: The date from which the ad becomes active. Leave empty for immediate activation.
                </p>
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
                    x-model="form.end_at"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Optional: The date the ad will expire. Must be on or after start date.
                </p>
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
                    x-model="form.is_active"
                    value="1"
                    class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                    checked
                >
                <label for="is_active" class="block text-sm font-medium text-gray-700">
                    Is Active?
                </label>
                <p class="text-xs text-gray-500 ml-8">
                    Check to enable the ad immediately upon saving.
                </p>
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
                    x-model="form.placement"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., header, sidebar, specific-page:/about"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Optional placement hints for where to show the ad on the site.
                </p>
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
                    x-model="form.targeting"
                    rows="3"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder='{"devices": ["mobile"], "countries": ["US"], "locations": ["sitewide"]}'
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 whitespace-pre-line">
                    Optional JSON rules to target ads, e.g.:  
                    {  
                    &nbsp;&nbsp;"devices": ["mobile", "desktop"],  
                    &nbsp;&nbsp;"countries": ["US", "GB"],  
                    &nbsp;&nbsp;"locations": ["homepage", "product-page"]  
                    }
                </p>
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
                    x-model="form.is_random"
                    value="1"
                    class="rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                >
                <label for="is_random" class="block text-sm font-medium text-gray-700">
                    Random Placement?
                </label>
                <p class="text-xs text-gray-500 ml-8">
                    Check if you want the ad to appear randomly among others in its placement.
                </p>
                @error('is_random')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center space-x-4 pt-4">
                <button
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <i class="bi bi-check2-circle me-2"></i> Create Ad
                </button>

                <a
                    href="{{ route('admin.ads.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-100"
                >
                    <i class="bi bi-x-circle me-2"></i> Cancel
                </a>
            </div>
        </form>

        {{-- Live Preview --}}
        <section class="mt-10 bg-gray-50 p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Live Preview</h3>

            <template x-if="form.type === 'image'">
                <div>
                    <img :src="form.content" :alt="form.title" class="max-w-full rounded shadow" />
                    <p class="mt-2 text-sm text-gray-600" x-text="form.title"></p>
                </div>
            </template>

            <template x-if="form.type === 'video'">
                <video controls class="w-full max-w-lg rounded shadow">
                    <source :src="form.content" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
                <p class="mt-2 text-sm text-gray-600" x-text="form.title"></p>
            </template>

            <template x-if="form.type === 'banner'">
                <div class="p-4 bg-white rounded shadow border" x-html="form.content"></div>
            </template>

            <template x-if="form.type === 'js'">
                <pre class="bg-gray-200 p-4 rounded text-xs overflow-auto" x-text="form.content"></pre>
            </template>

            <template x-if="form.type === 'popup' || form.type === 'interstitial'">
                <div class="p-4 bg-indigo-50 rounded shadow border italic text-gray-700">
                    Popup or Interstitial ads will appear as overlays after submission.
                </div>
            </template>

            <template x-if="!form.type">
                <div class="italic text-gray-400">Select a type to preview your ad here.</div>
            </template>
        </section>
    </div>

    <script>
        function adForm() {
            return {
                form: {
                    user_id: '{{ old('user_id') }}' || '',
                    product_id: '{{ old('product_id') }}' || '',
                    title: '{{ old('title') }}' || '',
                    type: '{{ old('type') }}' || '',
                    content: `{{ old('content') ?? '' }}`,
                    link: '{{ old('link') }}' || '',
                    start_at: '{{ old('start_at') }}' || '',
                    end_at: '{{ old('end_at') }}' || '',
                    is_active: {{ old('is_active', true) ? 'true' : 'false' }},
                    placement: '{{ old('placement') }}' || '',
                    targeting: `{{ old('targeting') ?? '' }}`,
                    is_random: {{ old('is_random') ? 'true' : 'false' }},
                },
                submitForm() {
                    // Disable form if you want; here just submit normally:
                    $el = event.target;
                    $el.submit();
                }
            };
        }
    </script>
</x-admin-layout>
