<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RentStreet' }} — RentStreet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-gray-900"
      x-data="{ sidebarOpen: false }">

    {{-- Mobile top bar --}}
    <header class="lg:hidden sticky top-0 z-30 flex items-center justify-between bg-white border-b px-4 py-3">
        <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="font-semibold">RentStreet</span>
        <span class="w-6"></span>
    </header>

    <div class="flex min-h-[calc(100vh-3rem)] lg:min-h-screen">

        {{-- Off-canvas drawer backdrop (mobile only) --}}
        <div x-show="sidebarOpen" x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside
            x-cloak
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r
                   transition-transform duration-200 ease-out
                   lg:translate-x-0 lg:static lg:z-auto">

            <div class="p-4 border-b flex items-center justify-between">
                <span class="font-bold text-lg">RentStreet</span>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="p-4 space-y-1">
                @yield('sidebar')
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
                <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                <div class="text-xs text-gray-500">{{ auth()->user()->role->label() }}</div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button class="text-sm text-red-600 hover:underline">Log out</button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <main class="flex-1 min-w-0">
            <div class="max-w-7xl mx-auto p-4 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>