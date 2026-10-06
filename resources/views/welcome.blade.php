@php
    /*
    |--------------------------------------------------------------------------
    | Stock images (Unsplash) — swap these IDs/URLs for real photos later.
    | SAMPLE listings below are placeholders; replace with a DB query when
    | your boarding_houses / rooms tables are populated.
    |--------------------------------------------------------------------------
    */
    $img = fn (string $id, int $w = 1200) =>
        "https://images.unsplash.com/{$id}?auto=format&fit=crop&w={$w}&q=70";

    $images = [
        'hero'     => $img('photo-1522708323590-d24dbb6b0267', 1400),
        'student'  => $img('photo-1523240795612-9a054b0db644', 900),
        'worker'   => $img('photo-1521737604893-d14cc237f11d', 900),
        'tourist'  => $img('photo-1507525428034-b723cf961d3e', 900),
        'other'    => $img('photo-1513694203232-719a280e022f', 900),
        'landlord' => $img('photo-1560448204-e02f11c3d0e2', 1400),
    ];

    $listings = [
        [
            'name'  => 'Casa Marina Boarding House',
            'area'  => 'Near the town center',
            'price' => '2,200',
            'type'  => 'Private room',
            'photo' => $img('photo-1555854877-bab0e564b8d5', 800),
            'icons' => [['wifi', 'Wi-Fi'], ['wind', 'Aircon'], ['video', 'CCTV']],
            'rules' => ['Curfew 10 PM', 'Mixed'],
        ],
        [
            'name'  => 'Tulay Residences',
            'area'  => 'Near SLSU campus',
            'price' => '1,500',
            'type'  => 'Shared room',
            'photo' => $img('photo-1502672260266-1c1ef2d93688', 800),
            'icons' => [['wifi', 'Wi-Fi'], ['flame', 'Cooking allowed'], ['video', 'CCTV']],
            'rules' => ['Curfew 9 PM', 'Female only'],
        ],
        [
            'name'  => 'Bayview Lodge',
            'area'  => 'Along the coastal road',
            'price' => '3,400',
            'type'  => 'Private room',
            'photo' => $img('photo-1505691938895-1758d7feb511', 800),
            'icons' => [['wifi', 'Wi-Fi'], ['wind', 'Aircon'], ['droplet', 'Own bathroom']],
            'rules' => ['No curfew', 'Mixed'],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RentStreet — Boarding houses in Sogod, Southern Leyte</title>
    <meta name="description" content="Find boarding houses and rooms in Sogod, Southern Leyte. Verified landlords, clear house rules, and privacy-first trust scores for students, workers, and visitors.">
    <meta name="theme-color" content="#0B3C49">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --bay: #0B3C49;      /* deep Sogod Bay water */
            --sea: #177E89;      /* shallow-water teal */
            --mist: #EEF3F2;     /* cool section background */
            --paper: #FAFBFA;    /* page background */
            --ink: #12242A;      /* body text */
            --muted: #566A70;    /* secondary text */
            --line: #D9E3E2;     /* borders */
            --lantern: #F2B544;  /* warm accent, used sparingly */
        }
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; background: var(--paper); color: var(--ink); }
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .bg-bay { background: var(--bay); }
        .bg-mist { background: var(--mist); }
        .bg-lantern { background: var(--lantern); }
        .text-bay { color: var(--bay); }
        .text-sea { color: var(--sea); }
        .text-muted { color: var(--muted); }
        .border-line { border-color: var(--line); }
        .ico { width: 1.25rem; height: 1.25rem; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex: none; }
        a:focus-visible, button:focus-visible, select:focus-visible, input:focus-visible {
            outline: 2px solid var(--sea); outline-offset: 2px;
        }
        .on-dark a:focus-visible, .on-dark button:focus-visible { outline-color: var(--lantern); }
        .img-wrap { background: var(--mist); }
    </style>
</head>
<body class="min-h-screen antialiased">

{{-- Icon sprite (line icons, no decorative/cartoon glyphs) --}}
<svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></symbol>
    <symbol id="i-wifi" viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/></symbol>
    <symbol id="i-wind" viewBox="0 0 24 24"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></symbol>
    <symbol id="i-video" viewBox="0 0 24 24"><path d="m16 13 5.22 3.48a.5.5 0 0 0 .78-.42V7.87a.5.5 0 0 0-.78-.42L16 11"/><rect x="2" y="6" width="14" height="12" rx="2"/></symbol>
    <symbol id="i-flame" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14-.22-4.05 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.43-2.29 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></symbol>
    <symbol id="i-droplet" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="i-bolt" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
    <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol>
    <symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-cap" viewBox="0 0 24 24"><path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></symbol>
    <symbol id="i-briefcase" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></symbol>
    <symbol id="i-compass" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36z"/></symbol>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></symbol>
    <symbol id="i-id" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
    <symbol id="i-bed" viewBox="0 0 24 24"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></symbol>
</svg>

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:shadow">Skip to content</a>

{{-- ============================== HEADER ============================== --}}
<header class="sticky top-0 z-40 border-b border-line bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="RentStreet home">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-bay text-white">
                <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
            </span>
            <span class="font-display text-xl font-bold text-bay">RentStreet</span>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium lg:flex" aria-label="Primary">
            <a href="#rooms" class="text-muted hover:text-bay">Rooms</a>
            <a href="#who" class="text-muted hover:text-bay">Who it's for</a>
            <a href="#trust" class="text-muted hover:text-bay">Trust and privacy</a>
            <a href="#how" class="text-muted hover:text-bay">How it works</a>
            <a href="#landlords" class="text-muted hover:text-bay">For landlords</a>
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-sm font-medium text-bay hover:bg-mist">Log in</a>
            <a href="{{ route('register') }}" class="rounded-md bg-bay px-4 py-2 text-sm font-medium text-white hover:bg-[#08303a]">Get started</a>
        </div>

        <button id="menuBtn" type="button" class="grid h-10 w-10 place-items-center rounded-md text-bay hover:bg-mist lg:hidden" aria-expanded="false" aria-controls="mobileMenu" aria-label="Open menu">
            <svg viewBox="0 0 24 24" class="ico" id="menuIconOpen"><use href="#i-menu"/></svg>
            <svg viewBox="0 0 24 24" class="ico hidden" id="menuIconClose"><use href="#i-x"/></svg>
        </button>
    </div>

    <div id="mobileMenu" class="hidden border-t border-line bg-white lg:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col px-5 py-3 text-base font-medium sm:px-8" aria-label="Mobile">
            <a href="#rooms" class="py-3 text-bay">Rooms</a>
            <a href="#who" class="py-3 text-bay">Who it's for</a>
            <a href="#trust" class="py-3 text-bay">Trust and privacy</a>
            <a href="#how" class="py-3 text-bay">How it works</a>
            <a href="#landlords" class="py-3 text-bay">For landlords</a>
            <div class="mt-2 grid grid-cols-2 gap-3 pb-3">
                <a href="{{ route('login') }}" class="rounded-md border border-line py-3 text-center text-bay">Log in</a>
                <a href="{{ route('register') }}" class="rounded-md bg-bay py-3 text-center text-white">Get started</a>
            </div>
        </nav>
    </div>
</header>

<main id="main">

{{-- ============================== HERO ============================== --}}
<section class="relative">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-2 pb-10 pt-10 sm:px-8 sm:pt-14 lg:grid-cols-12 lg:gap-14 lg:pb-28 lg:pt-10">
        <div class="lg:col-span-6">

            <h1 class="font-display mt-5 text-[2.5rem] font-bold leading-[1.05] text-bay sm:text-6xl lg:text-[4.25rem]">
                Find a room in Sogod that fits how you live.
            </h1>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted">
                Browse boarding houses from landlords checked by an admin. See the curfew, cooking rules, and price before you visit, whether you are studying, working, or just passing through.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="#rooms" class="inline-flex items-center justify-center gap-2 rounded-md bg-bay px-6 py-3.5 text-base font-medium text-white hover:bg-[#08303a]">
                    Browse rooms
                    <svg viewBox="0 0 24 24" class="ico"><use href="#i-arrow"/></svg>
                </a>
                <a href="{{ route('register', ['role' => 'landlord']) }}" class="inline-flex items-center justify-center rounded-md border border-line bg-white px-6 py-3.5 text-base font-medium text-bay hover:bg-mist">
                    List your boarding house
                </a>
            </div>
        </div>

        <div class="relative lg:col-span-6">
            <div class="img-wrap aspect-[4/3] overflow-hidden rounded-[2rem] rounded-tr-[5rem] lg:aspect-[5/6] lg:rounded-tr-[8rem]">
                <img src="{{ $images['hero'] }}" alt="A bright, tidy room with a bed and a window" class="h-full w-full object-cover" fetchpriority="high" onerror="this.style.visibility='hidden'">
            </div>
            <div class="absolute -bottom-6 left-4 flex max-w-[17rem] items-start gap-3 rounded-xl border border-line bg-white p-4 shadow-lg sm:left-6">
                <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-mist text-sea">
                    <svg viewBox="0 0 24 24" class="ico"><use href="#i-shield"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-bay">Approved landlord</p>
                    <p class="mt-0.5 text-sm leading-snug text-muted">Valid ID and business permit reviewed by an admin.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search panel --}}
    <div class="relative z-10 mx-auto max-w-7xl px-5 sm:px-8 lg:-mt-14">
        <form action="{{ url('/properties') }}" method="GET" class="rounded-2xl border border-line bg-white p-4 shadow-xl sm:p-6">
            <fieldset>
                <legend class="mb-3 text-sm font-medium text-muted">I am looking for a room as a</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach (['student' => 'Student', 'worker' => 'Worker', 'tourist' => 'Tourist', 'other' => 'Other'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="renter_type" value="{{ $value }}" class="peer sr-only" @checked($value === 'student')>
                            <span class="block rounded-full border border-line px-4 py-2 text-sm font-medium text-bay peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_auto]">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-bay">Area or landmark</span>
                    <span class="flex items-center gap-2 rounded-md border border-line px-3 focus-within:border-[#177E89]">
                        <svg viewBox="0 0 24 24" class="ico text-muted"><use href="#i-pin"/></svg>
                        <input type="text" name="q" placeholder="Sogod town center, SLSU" class="w-full border-0 bg-transparent py-3 text-base placeholder:text-gray-400 focus:outline-none focus:ring-0">
                    </span>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-bay">Monthly budget</span>
                    <select name="max_price" class="w-full rounded-md border border-line bg-white px-3 py-3 text-base">
                        <option value="">Any budget</option>
                        <option value="1500">Up to ₱1,500</option>
                        <option value="2500">Up to ₱2,500</option>
                        <option value="3500">Up to ₱3,500</option>
                        <option value="5000">Up to ₱5,000</option>
                    </select>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-bay">Room type</span>
                    <select name="room_type" class="w-full rounded-md border border-line bg-white px-3 py-3 text-base">
                        <option value="">Private or shared</option>
                        <option value="private">Private room</option>
                        <option value="shared">Shared room</option>
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-md bg-bay px-6 py-3 text-base font-medium text-white hover:bg-[#08303a] lg:w-auto">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#i-search"/></svg>
                        Search rooms
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- ============================== ASSURANCES ============================== --}}
<section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">
    <div class="grid gap-8 md:grid-cols-3">
        @foreach ([
            ['id', 'Landlords are checked first', 'Every landlord submits a valid ID and business permit. An admin approves or declines, and gives a reason if declined.'],
            ['clock', 'House rules up front', 'Curfew, cooking, gender policy, and water supply sit on every listing, so you can rule rooms out from your phone.'],
            ['lock', 'Your data stays yours', 'Built around the Data Privacy Act (RA 10173). Your ID is for verification and your trust score is not public.'],
        ] as [$icon, $title, $body])
            <div class="flex gap-4">
                <span class="grid h-11 w-11 flex-none place-items-center rounded-lg bg-mist text-sea">
                    <svg viewBox="0 0 24 24" class="ico"><use href="#i-{{ $icon }}"/></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-bay">{{ $title }}</h2>
                    <p class="mt-1.5 leading-relaxed text-muted">{{ $body }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================== LISTINGS ============================== --}}
<section id="rooms" class="bg-mist py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h2 class="font-display text-3xl font-bold text-bay sm:text-4xl">Boarding houses to look at first</h2>
                <p class="mt-3 max-w-xl text-lg text-muted">Real prices per month, with the details people usually have to message to ask about.</p>
            </div>
            <a href="{{ url('/properties') }}" class="inline-flex items-center gap-2 text-base font-medium text-bay underline-offset-4 hover:underline">
                See all boarding houses
                <svg viewBox="0 0 24 24" class="ico"><use href="#i-arrow"/></svg>
            </a>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($listings as $home)
                <article class="group overflow-hidden rounded-2xl border border-line bg-white">
                    <div class="img-wrap relative aspect-[4/3] overflow-hidden">
                        <img src="{{ $home['photo'] }}" alt="Room at {{ $home['name'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" onerror="this.style.visibility='hidden'">
                        <span class="absolute left-3 top-3 rounded-full bg-white px-3 py-1 text-sm font-medium text-bay">{{ $home['type'] }}</span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-display text-lg font-bold leading-snug text-bay">{{ $home['name'] }}</h3>
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-muted">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#i-pin"/></svg>
                                    {{ $home['area'] }}
                                </p>
                            </div>
                            <p class="text-right">
                                <span class="font-display text-xl font-bold text-bay">₱{{ $home['price'] }}</span>
                                <span class="block text-sm text-muted">per month</span>
                            </p>
                        </div>

                        <ul class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm text-ink">
                            @foreach ($home['icons'] as [$icon, $label])
                                <li class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#i-{{ $icon }}"/></svg>{{ $label }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 flex flex-wrap gap-2 border-t border-line pt-4">
                            @foreach ($home['rules'] as $rule)
                                <span class="rounded-md bg-mist px-2.5 py-1 text-sm text-bay">{{ $rule }}</span>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== WHO IT'S FOR ============================== --}}
<section id="who" class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-24">
    <div class="max-w-2xl">
        <h2 class="font-display text-3xl font-bold text-bay sm:text-4xl">Not just for students</h2>
        <p class="mt-3 text-lg text-muted">RentStreet started near campus and now fits anyone who needs a room in Sogod, for a few nights or a few years.</p>
    </div>

    <div class="mt-10 grid gap-5 md:grid-cols-6 md:grid-rows-2">
        @php
            $who = [
                ['student', 'cap', 'Students', 'Rooms near campus with curfews and house rules that suit student life.', 'md:col-span-3 md:row-span-2', 'min-h-[22rem]'],
                ['worker', 'briefcase', 'Workers', 'Monthly stays close to work, with Wi-Fi and cooking rules listed upfront.', 'md:col-span-3', 'min-h-[16rem]'],
                ['tourist', 'compass', 'Tourists', 'Short stays for less than a hotel. Tell landlords how long you plan to stay.', 'md:col-span-2', 'min-h-[16rem]'],
                ['other', 'home', 'Everyone else', 'Relocating or between homes? Set your budget and find what fits.', 'md:col-span-1', 'min-h-[16rem]'],
            ];
        @endphp
        @foreach ($who as [$key, $icon, $title, $text, $span, $height])
            <article class="img-wrap group relative isolate overflow-hidden rounded-2xl {{ $span }} {{ $height }}">
                <img src="{{ $images[$key] }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#0B3C49] via-[#0B3C49]/55 to-transparent"></div>
                <div class="flex h-full flex-col justify-end p-6 text-white">
                    <span class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-white/15 backdrop-blur">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#i-{{ $icon }}"/></svg>
                    </span>
                    <h3 class="font-display text-2xl font-bold">{{ $title }}</h3>
                    <p class="mt-1.5 max-w-sm leading-relaxed text-white/90">{{ $text }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ============================== HOUSE RULES / FILTERS ============================== --}}
<section class="bg-mist py-16 lg:py-24">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-2 lg:gap-20">
        <div>
            <h2 class="font-display text-3xl font-bold text-bay sm:text-4xl">Check the house rules before you visit</h2>
            <p class="mt-4 max-w-lg text-lg leading-relaxed text-muted">
                Filter by what matters to your daily routine. Choose your filters, then apply them once, so the list does not jump around while you decide.
            </p>
            <a href="{{ url('/properties') }}" class="mt-7 inline-flex items-center gap-2 rounded-md bg-bay px-6 py-3.5 text-base font-medium text-white hover:bg-[#08303a]">
                Try the filters
                <svg viewBox="0 0 24 24" class="ico"><use href="#i-arrow"/></svg>
            </a>
        </div>

        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach ([
                ['clock', 'Curfew time', 'Know when the gate closes'],
                ['flame', 'Cooking allowed', 'Or shared kitchen rules'],
                ['users', 'Gender policy', 'Male only, female only, or mixed'],
                ['droplet', 'Water supply', 'Rated by the landlord'],
                ['bolt', 'Sub-metered power', 'Pay for what you use'],
                ['wifi', 'Amenities', 'Wi-Fi, CCTV, aircon, and more'],
            ] as [$icon, $title, $hint])
                <li class="flex items-center gap-4 rounded-xl border border-line bg-white p-4">
                    <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#i-{{ $icon }}"/></svg>
                    </span>
                    <div>
                        <p class="font-semibold text-bay">{{ $title }}</p>
                        <p class="text-sm text-muted">{{ $hint }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ============================== TRUST & PRIVACY ============================== --}}
<section id="trust" class="on-dark bg-bay py-16 text-white lg:py-24">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-2 lg:gap-20">
        <div>
            <h2 class="font-display text-3xl font-bold sm:text-4xl">A trust score that respects your privacy</h2>
            <p class="mt-4 max-w-lg text-lg leading-relaxed text-white/80">
                Renters build a record through on-time rent and fair move-outs. It is a log of what happened, not a number a landlord can edit.
            </p>

            <ul class="mt-9 space-y-6">
                @foreach ([
                    ['lock', 'Private until you apply', 'A landlord can see your score only after you apply to one of their rooms.'],
                    ['eye', 'Every view is logged', 'Each time a landlord opens your score, it is recorded, as the Data Privacy Act expects.'],
                    ['chat', 'You can dispute a mark', 'Disagree with a negative entry? Upload evidence and an admin reviews it.'],
                ] as [$icon, $title, $body])
                    <li class="flex gap-4">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-white/10 text-[#F2B544]">
                            <svg viewBox="0 0 24 24" class="ico"><use href="#i-{{ $icon }}"/></svg>
                        </span>
                        <div>
                            <h3 class="font-semibold">{{ $title }}</h3>
                            <p class="mt-1 leading-relaxed text-white/75">{{ $body }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Ledger preview --}}
        <div class="rounded-2xl bg-white p-5 text-ink shadow-2xl sm:p-7" aria-label="Example of a trust record">
            <div class="flex items-center justify-between border-b border-line pb-4">
                <div>
                    <p class="text-sm text-muted">Trust record</p>
                    <p class="font-display text-3xl font-bold text-bay">100.00</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-mist px-3 py-1.5 text-sm font-medium text-bay">
                    <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#i-lock"/></svg>
                    Visible after you apply
                </span>
            </div>

            <ul class="divide-y divide-[#D9E3E2]">
                @foreach ([
                    ['Rent paid on time', 'Added to record', 'text-sea'],
                    ['Checked out as agreed', 'Added to record', 'text-sea'],
                    ['Late payment, disputed', 'Under admin review', 'text-muted'],
                    ['Landlord viewed score', 'Access logged', 'text-muted'],
                ] as [$event, $status, $tone])
                    <li class="flex items-center justify-between gap-4 py-3.5">
                        <span class="font-medium text-bay">{{ $event }}</span>
                        <span class="text-sm {{ $tone }}">{{ $status }}</span>
                    </li>
                @endforeach
            </ul>

            <p class="mt-2 rounded-lg bg-mist p-3 text-sm leading-relaxed text-muted">
                Entries are added, never rewritten. A landlord can add to the record but cannot change the score directly.
            </p>
        </div>
    </div>
</section>

{{-- ============================== HOW IT WORKS ============================== --}}
<section id="how" class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-24">
    <h2 class="font-display max-w-2xl text-3xl font-bold text-bay sm:text-4xl">How RentStreet works</h2>

    <div class="mt-10 grid gap-6 lg:grid-cols-2">
        {{-- Renters --}}
        <div class="rounded-2xl border border-line bg-white p-6 sm:p-8">
            <h3 class="font-display text-xl font-bold text-bay">If you are looking for a room</h3>
            <ol class="mt-6 space-y-6">
                @foreach ([
                    ['Search and save', 'Filter by budget, room type, and house rules. Save favorite boarding houses to compare later.'],
                    ['Apply to a room', 'Pick a specific room and add an optional message. You can have one open application per room, and you can withdraw anytime.'],
                    ['Follow your application', 'See when it is submitted, viewed by the landlord, and accepted or declined.'],
                ] as $i => [$title, $body])
                    <li class="flex gap-4">
                        <span class="grid h-8 w-8 flex-none place-items-center rounded-full bg-bay text-sm font-semibold text-white">{{ $i + 1 }}</span>
                        <div>
                            <p class="font-semibold text-bay">{{ $title }}</p>
                            <p class="mt-1 leading-relaxed text-muted">{{ $body }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <div class="mt-7 flex flex-wrap gap-2 border-t border-line pt-5 text-sm">
                @foreach (['Submitted', 'Viewed', 'Accepted'] as $s)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-mist px-3 py-1.5 font-medium text-bay">
                        <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#i-check"/></svg>{{ $s }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Landlords --}}
        <div class="rounded-2xl border border-line bg-white p-6 sm:p-8">
            <h3 class="font-display text-xl font-bold text-bay">If you own a boarding house</h3>
            <ol class="mt-6 space-y-6">
                @foreach ([
                    ['Register and verify', 'Create your account and upload a valid ID and business permit. Add your GCash or Maya number for payments.'],
                    ['Get approved', 'An admin reviews your account. If something is missing, you will see the reason and can fix it and resubmit.'],
                    ['List your rooms', 'Add photos, amenities, prices, and house rules. Edits to a declined listing send it back for review automatically.'],
                ] as $i => [$title, $body])
                    <li class="flex gap-4">
                        <span class="grid h-8 w-8 flex-none place-items-center rounded-full bg-bay text-sm font-semibold text-white">{{ $i + 1 }}</span>
                        <div>
                            <p class="font-semibold text-bay">{{ $title }}</p>
                            <p class="mt-1 leading-relaxed text-muted">{{ $body }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- ============================== LANDLORD CTA ============================== --}}
<section id="landlords" class="px-5 pb-16 sm:px-8 lg:pb-24">
    <div class="on-dark img-wrap relative isolate mx-auto max-w-7xl overflow-hidden rounded-[2rem]">
        <img src="{{ $images['landlord'] }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/30"></div>
        <div class="max-w-xl px-6 py-14 text-white sm:px-12 sm:py-20">
            <h2 class="font-display text-3xl font-bold sm:text-4xl">Fill your rooms with renters who read the rules</h2>
            <p class="mt-4 text-lg leading-relaxed text-white/85">
                List your boarding house on a subscription plan. Renters see your curfew, cooking policy, and amenities before they apply, so the messages you get are from people who fit.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register', ['role' => 'landlord']) }}" class="bg-lantern inline-flex items-center justify-center rounded-md px-6 py-3.5 text-base font-semibold text-[#12242A] hover:brightness-95">
                    Register as a landlord
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md border border-white/40 px-6 py-3.5 text-base font-medium text-white hover:bg-white/10">
                    Log in to your account
                </a>
            </div>
        </div>
    </div>
</section>

</main>

{{-- ============================== FOOTER ============================== --}}
<footer class="border-t border-line bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 sm:px-8 md:grid-cols-[1.5fr_1fr_1fr]">
        <div>
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-bay text-white">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-bay">RentStreet</span>
            </a>
            <p class="mt-4 max-w-sm leading-relaxed text-muted">
                Boarding houses and rooms in Sogod, Southern Leyte, Philippines.
            </p>
        </div>
        <nav aria-label="Renters">
            <p class="font-semibold text-bay">For renters</p>
            <ul class="mt-3 space-y-2 text-muted">
                <li><a href="{{ url('/properties') }}" class="hover:text-bay">Browse rooms</a></li>
                <li><a href="#trust" class="hover:text-bay">Trust and privacy</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-bay">Create an account</a></li>
            </ul>
        </nav>
        <nav aria-label="Landlords">
            <p class="font-semibold text-bay">For landlords</p>
            <ul class="mt-3 space-y-2 text-muted">
                <li><a href="{{ route('register', ['role' => 'landlord']) }}" class="hover:text-bay">List your boarding house</a></li>
                <li><a href="#how" class="hover:text-bay">How approval works</a></li>
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
        var btn = document.getElementById('menuBtn');
        var menu = document.getElementById('mobileMenu');
        var open = document.getElementById('menuIconOpen');
        var close = document.getElementById('menuIconClose');
        if (!btn) return;

        function setOpen(isOpen) {
            menu.classList.toggle('hidden', !isOpen);
            open.classList.toggle('hidden', isOpen);
            close.classList.toggle('hidden', !isOpen);
            btn.setAttribute('aria-expanded', String(isOpen));
            btn.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        }

        btn.addEventListener('click', function () {
            setOpen(btn.getAttribute('aria-expanded') !== 'true');
        });
        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { setOpen(false); });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    })();
</script>
</body>
</html>