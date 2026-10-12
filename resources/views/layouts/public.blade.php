<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B3C49">
    <title>@yield('title', 'Browse rooms') — RentStreet</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('logo_nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bay: #0B3C49; --sea: #177E89; --mist: #EEF3F2; --paper: #FAFBFA;
            --ink: #12242A; --muted: #566A70; --line: #D9E3E2; --lantern: #F2B544;
        }
        [x-cloak] { display: none !important; }
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; background: var(--paper); color: var(--ink); }
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .bg-bay { background: var(--bay); } .bg-mist { background: var(--mist); } .bg-lantern { background: var(--lantern); }
        .text-bay { color: var(--bay); } .text-sea { color: var(--sea); } .text-muted { color: var(--muted); }
        .border-line { border-color: var(--line); }
        .ico { width: 1.25rem; height: 1.25rem; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex: none; }
        a:focus-visible, button:focus-visible, select:focus-visible, input:focus-visible { outline: 2px solid var(--sea); outline-offset: 2px; }
    </style>
</head>
<body class="min-h-screen antialiased">

<svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></symbol>
    <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
    <symbol id="i-image" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/></symbol>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
    <symbol id="i-back" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="i-flame" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14-.22-4.05 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.43-2.29 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></symbol>
    <symbol id="i-bolt" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></symbol>
    <symbol id="i-bed" viewBox="0 0 24 24"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></symbol>
</svg>

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:shadow">Skip to content</a>

{{-- ============================== HEADER (same as homepage) ============================== --}}
<header class="sticky top-0 z-40 border-b border-line bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="RentStreet home">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-bay text-white">
                <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
            </span>
            <span class="font-display text-xl font-bold text-bay">RentStreet</span>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium lg:flex" aria-label="Primary">
            <a href="{{ route('properties.index') }}"
               class="{{ request()->routeIs('properties.*') ? 'text-bay' : 'text-muted hover:text-bay' }}"
               @if (request()->routeIs('properties.*')) aria-current="page" @endif>Rooms</a>
            <a href="{{ url('/') }}#who" class="text-muted hover:text-bay">Who it's for</a>
            <a href="{{ url('/') }}#trust" class="text-muted hover:text-bay">Trust and privacy</a>
            <a href="{{ url('/') }}#how" class="text-muted hover:text-bay">How it works</a>
            <a href="{{ url('/') }}#landlords" class="text-muted hover:text-bay">For landlords</a>
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <a href="{{ url('/dashboard') }}" class="rounded-md bg-bay px-4 py-2 text-sm font-medium text-white hover:bg-[#08303a]">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-sm font-medium text-bay hover:bg-mist">Log in</a>
                <a href="{{ route('register') }}" class="rounded-md bg-bay px-4 py-2 text-sm font-medium text-white hover:bg-[#08303a]">Get started</a>
            @endauth
        </div>

        <button id="menuBtn" type="button" class="grid h-10 w-10 place-items-center rounded-md text-bay hover:bg-mist lg:hidden" aria-expanded="false" aria-controls="mobileMenu" aria-label="Open menu">
            <svg viewBox="0 0 24 24" class="ico" id="menuIconOpen"><use href="#i-menu"/></svg>
            <svg viewBox="0 0 24 24" class="ico hidden" id="menuIconClose"><use href="#i-x"/></svg>
        </button>
    </div>

    <div id="mobileMenu" class="hidden border-t border-line bg-white lg:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col px-5 py-3 text-base font-medium sm:px-8" aria-label="Mobile">
            <a href="{{ route('properties.index') }}" class="py-3 text-bay">Rooms</a>
            <a href="{{ url('/') }}#who" class="py-3 text-bay">Who it's for</a>
            <a href="{{ url('/') }}#trust" class="py-3 text-bay">Trust and privacy</a>
            <a href="{{ url('/') }}#how" class="py-3 text-bay">How it works</a>
            <a href="{{ url('/') }}#landlords" class="py-3 text-bay">For landlords</a>
            @guest
                <div class="mt-2 grid grid-cols-2 gap-3 pb-3">
                    <a href="{{ route('login') }}" class="rounded-md border border-line py-3 text-center text-bay">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-bay py-3 text-center text-white">Get started</a>
                </div>
            @endguest
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

{{-- ============================== FOOTER (same as homepage) ============================== --}}
<footer class="mt-16 border-t border-line bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 sm:px-8 md:grid-cols-[1.5fr_1fr_1fr]">
        <div>
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-bay text-white">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-bay">RentStreet</span>
            </a>
            <p class="mt-4 max-w-sm leading-relaxed text-muted">Boarding houses and rooms in Sogod, Southern Leyte, Philippines.</p>
        </div>
        <nav aria-label="Renters">
            <p class="font-semibold text-bay">For renters</p>
            <ul class="mt-3 space-y-2 text-muted">
                <li><a href="{{ route('properties.index') }}" class="hover:text-bay">Browse rooms</a></li>
                <li><a href="{{ url('/') }}#trust" class="hover:text-bay">Trust and privacy</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-bay">Create an account</a></li>
            </ul>
        </nav>
        <nav aria-label="Landlords">
            <p class="font-semibold text-bay">For landlords</p>
            <ul class="mt-3 space-y-2 text-muted">
                <li><a href="{{ route('register', ['role' => 'landlord']) }}" class="hover:text-bay">List your boarding house</a></li>
                <li><a href="{{ url('/') }}#how" class="hover:text-bay">How approval works</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-bay">Log in</a></li>
            </ul>
        </nav>
    </div>
    <div class="border-t border-line">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-5 text-sm text-muted sm:px-8 md:flex-row md:justify-between">
            <p>&copy; {{ date('Y') }} RentStreet. All rights reserved.</p>
            <p>Personal data is handled in line with the Data Privacy Act of 2012 (RA 10173).</p>
        </div>
    </div>
</footer>

<script>
    (function () {
        var btn = document.getElementById('menuBtn'), menu = document.getElementById('mobileMenu'),
            open = document.getElementById('menuIconOpen'), close = document.getElementById('menuIconClose');
        if (!btn) return;
        function setOpen(o) {
            menu.classList.toggle('hidden', !o); open.classList.toggle('hidden', o); close.classList.toggle('hidden', !o);
            btn.setAttribute('aria-expanded', String(o)); btn.setAttribute('aria-label', o ? 'Close menu' : 'Open menu');
        }
        btn.addEventListener('click', function () { setOpen(btn.getAttribute('aria-expanded') !== 'true'); });
        menu.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
    })();
</script>
</body>
</html>