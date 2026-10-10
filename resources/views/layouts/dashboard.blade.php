<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B3C49">
    <title>@yield('title', $title ?? 'Dashboard') — RentStreet</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --bay: #0B3C49; --sea: #177E89; --mist: #EEF3F2; --paper: #FAFBFA;
            --ink: #12242A; --muted: #566A70; --line: #D9E3E2; --lantern: #F2B544;
            --danger: #A32A2A; --danger-bg: #FBEFEE;
        }
        [x-cloak] { display: none !important; }
        .rs-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; color: var(--ink); background: var(--paper); }
        .rs-page .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .bg-bay { background: var(--bay); } .bg-mist { background: var(--mist); }
        .text-bay { color: var(--bay); } .text-sea { color: var(--sea); }
        .text-muted { color: var(--muted); } .text-danger { color: var(--danger); }
        .border-line { border-color: var(--line); }
        .ico { width: 1.25rem; height: 1.25rem; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex: none; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; height: 2.5rem; padding: 0 1rem;
               border-radius: .375rem; font-size: .875rem; font-weight: 500; line-height: 1; cursor: pointer; transition: background-color .15s, border-color .15s; white-space: nowrap; }
        .btn-lg { height: 3rem; font-size: 1rem; }
        .btn-primary { background: var(--bay); color: #fff; } .btn-primary:hover { background: #08303a; }
        .btn-secondary { background: #fff; color: var(--bay); border: 1px solid var(--line); } .btn-secondary:hover { background: var(--mist); }
        .btn-danger { background: #fff; color: var(--danger); border: 1px solid #E7C4C1; } .btn-danger:hover { background: var(--danger-bg); }
        .btn-danger-solid { background: var(--danger); color: #fff; } .btn-danger-solid:hover { background: #862222; }
        .btn:disabled { opacity: .65; cursor: not-allowed; }

        .field { width: 100%; height: 3rem; border-radius: .375rem; border: 1px solid var(--line); background: #fff; padding: 0 .875rem; font-size: 1rem; color: var(--ink); box-shadow: none; }
        .field:focus { border-color: var(--sea); box-shadow: 0 0 0 3px rgb(23 126 137 / .18); outline: none; }
        .field[aria-invalid="true"] { border-color: var(--danger); }

        .rs-page a:focus-visible, .rs-page button:focus-visible { outline: 2px solid var(--sea); outline-offset: 2px; }
        aside a:focus-visible, aside button:focus-visible { outline-color: var(--lantern) !important; }
        .choice-card { transition: background-color .15s, border-color .15s; }
        [data-invalid] .choice-card { border-color: var(--danger); }
    </style>
</head>
<body class="rs-page min-h-screen antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false"
      :class="{ 'overflow-hidden lg:overflow-visible': sidebarOpen }">

    {{-- Icon sprite --}}
    <svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
        <symbol id="l-home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></symbol>
        <symbol id="l-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="l-minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
        <symbol id="l-back" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
        <symbol id="l-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
        <symbol id="l-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
        <symbol id="l-check-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="l-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></symbol>
        <symbol id="l-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></symbol>
        <symbol id="l-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="l-shield" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="l-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
        <symbol id="l-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="l-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></symbol>
        <symbol id="l-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></symbol>
        <symbol id="l-building" viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1"/><path d="M10 21v-4h4v4"/></symbol>
        <symbol id="l-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></symbol>
        <symbol id="l-user-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M7 20.66V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.66"/></symbol>
        <symbol id="l-bed" viewBox="0 0 24 24"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></symbol>
        <symbol id="l-user" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="l-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
        <symbol id="l-droplet" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></symbol>
        <symbol id="l-wind" viewBox="0 0 24 24"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></symbol>
        <symbol id="l-pencil" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
        <symbol id="l-trash" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6"/><path d="M14 11v6"/></symbol>
        <symbol id="l-image" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/></symbol>
        <symbol id="l-upload" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></symbol>
        <symbol id="l-handshake" viewBox="0 0 24 24"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5-2.5a1.5 1.5 0 0 0-2.12-2.12L12 12"/><path d="M2 12s3-3 7-3c1.5 0 2.8.5 4 1.5"/><path d="M22 12s-3-3-7-3c-1.5 0-2.8.5-4 1.5"/><path d="M19 19c-2 2-5 2-7 0L9 16l3-3 3 3 4 3Z"/></symbol>
    </svg>

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:shadow">Skip to content</a>

    @php
        $navUser  = auth()->user();
        $initials = collect(preg_split('/\s+/', trim($navUser->name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    @endphp

    {{-- ================= Mobile top bar ================= --}}
    <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-line bg-white/95 px-4 backdrop-blur lg:hidden">
        <button type="button" @click="sidebarOpen = true"
                class="-ml-2 grid h-10 w-10 place-items-center rounded-md text-bay hover:bg-mist"
                aria-label="Open menu" aria-controls="sidebar" :aria-expanded="sidebarOpen.toString()">
            <svg viewBox="0 0 24 24" class="ico"><use href="#l-menu"/></svg>
        </button>
        <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="RentStreet home">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-bay text-white">
                <svg viewBox="0 0 24 24" class="ico" style="width:1.1rem;height:1.1rem"><use href="#l-home"/></svg>
            </span>
            <span class="font-display text-lg font-bold text-bay">RentStreet</span>
        </a>
        <span class="grid h-9 w-9 place-items-center rounded-full bg-mist text-sm font-semibold text-bay" aria-hidden="true">{{ $initials }}</span>
    </header>

    <div class="lg:flex lg:min-h-screen">

        {{-- Drawer backdrop (mobile) --}}
        <div x-show="sidebarOpen" x-cloak x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-[#0B3C49]/50 lg:hidden" aria-hidden="true"></div>

        {{-- ================= Sidebar ================= --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col bg-bay text-white transition-transform duration-200 ease-out motion-reduce:transition-none lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-64 lg:max-w-none lg:flex-none lg:translate-x-0"
               :class="{ '-translate-x-full': !sidebarOpen }"
               aria-label="Sidebar">

            <div class="flex h-16 flex-none items-center justify-between border-b border-white/10 px-5">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="RentStreet home">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-white text-bay">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#l-home"/></svg>
                    </span>
                    <span class="font-display text-xl font-bold">RentStreet</span>
                </a>
                <button type="button" @click="sidebarOpen = false"
                        class="-mr-2 grid h-9 w-9 place-items-center rounded-md text-white/80 hover:bg-white/10 lg:hidden"
                        aria-label="Close menu">
                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-x"/></svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Main">
                @yield('sidebar')
            </nav>

            <div class="flex-none border-t border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 flex-none place-items-center rounded-full bg-white/10 text-sm font-semibold" aria-hidden="true">{{ $initials }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">{{ $navUser->name }}</p>
                        <p class="text-xs text-white/60">{{ $navUser->role->label() }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/70 transition-colors hover:bg-white/5 hover:text-white">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-logout"/></svg>
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        {{-- ================= Main ================= --}}
        <main id="main" class="min-w-0 flex-1">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-10 lg:py-10">
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay" role="status">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#l-check-circle"/></svg>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>