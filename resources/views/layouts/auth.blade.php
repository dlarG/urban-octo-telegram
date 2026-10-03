<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Welcome' }} — RentStreet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-gray-900">

    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- Brand panel (hidden on mobile, side panel on desktop) --}}
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white p-12 flex-col justify-between">
            <a href="/" class="text-2xl font-bold tracking-tight">RentStreet</a>

            <div>
                <h1 class="text-3xl font-semibold leading-tight">
                    Find your next home<br>in Sogod, Southern Leyte.
                </h1>
                <p class="mt-4 text-indigo-100 max-w-md">
                    Boarding houses, bed spaces, and rentals — vetted landlords, transparent pricing, and a trust score you can count on.
                </p>
            </div>

            <div class="text-sm text-indigo-200">
                © {{ date('Y') }} RentStreet. All rights reserved.
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">

                {{-- Mobile-only brand --}}
                <div class="lg:hidden mb-8 text-center">
                    <a href="/" class="text-2xl font-bold text-indigo-700">RentStreet</a>
                    <p class="text-sm text-gray-500 mt-1">Find your next home in Sogod.</p>
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>