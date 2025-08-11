<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap Icons CDN -->
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />

        {{-- alpinejs --}}
        <script
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
            defer
        ></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div
                        class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
                    >
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            {{-- Popup and Interstitial Ads --}}
            @if(isset($ads) && $ads->isNotEmpty())
                @foreach($ads as $ad)
                    @if($ad->type === 'popup')
                        <div
                            x-data="{ open: true }"
                            x-show="open"
                            class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
                            style="display: none;"
                            x-transition
                        >
                            <div
                                class="bg-white p-6 rounded shadow max-w-md mx-auto text-center"
                            >
                                {!! $ad->content !!}
                                <button
                                    @click="open = false"
                                    class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    @elseif($ad->type === 'interstitial')
                        <div
                            x-data="{ open: false }"
                            x-init="setTimeout(() => open = true, 2000)"
                            x-show="open"
                            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
                            style="display: none;"
                            x-transition
                        >
                            <div
                                class="bg-white p-6 rounded shadow max-w-lg mx-auto relative"
                            >
                                {!! $ad->content !!}
                                <button
                                    @click="open = false"
                                    class="absolute top-2 right-2 text-gray-700 hover:text-gray-900"
                                >
                                    &times;
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </body>
</html>
