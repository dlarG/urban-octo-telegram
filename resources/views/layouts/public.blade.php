<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RentStreet' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50">

    <header class="bg-white border-b">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('properties.index') }}" class="text-lg font-bold text-indigo-700">RentStreet</a>

            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-indigo-600">Browse</a>
                @auth
                    <a href="{{ route(auth()->user()->role->value . '.dashboard') }}"
                       class="text-gray-700 hover:text-indigo-600">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:underline">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Log in</a>
                    <a href="{{ route('register') }}"
                       class="rounded-md bg-indigo-600 px-3 py-1.5 text-white font-medium hover:bg-indigo-700">
                        Sign up
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('status'))
            <div class="max-w-6xl mx-auto px-4 pt-6">
                <div class="rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-16 border-t bg-white">
        <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-gray-500">
            © {{ date('Y') }} RentStreet. Sogod, Southern Leyte.
        </div>
    </footer>

    @livewireScripts
</body>
</html>