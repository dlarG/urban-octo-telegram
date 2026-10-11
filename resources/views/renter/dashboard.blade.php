@extends('layouts.renter')

@section('title', 'Dashboard')

@section('content')
    @php
        $user       = auth()->user();
        $firstName  = \Illuminate\Support\Str::of($user->name)->before(' ');
        $scoreRow   = $user->trustScore;
        $hasScore   = $scoreRow !== null;
        $scoreValue = $hasScore ? (float) $scoreRow->score : null;

        // Pass these from the controller when they are ready; until then the tiles show a dash.
        $applicationsCount = $applicationsCount ?? null;
        $favoritesCount    = $favoritesCount ?? null;

        $pct       = $hasScore ? max(0, min(100, $scoreValue)) : 0;
        $circ      = 2 * pi() * 52;
        $ringColor = $pct >= 75 ? '#177E89' : ($pct >= 50 ? '#C48A1A' : '#A32A2A');
    @endphp

    <div class="space-y-8">

        {{-- ================= Welcome ================= --}}
        <section class="relative isolate overflow-hidden rounded-3xl bg-bay text-white">
            <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70"
                 alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/30"></div>

            <div class="max-w-xl px-6 py-10 sm:px-10 sm:py-14">
                <p class="text-sm font-medium text-white/80">Welcome back</p>
                <h1 class="font-display mt-1 text-3xl font-bold sm:text-4xl">Hi, {{ $firstName }}</h1>
                <p class="mt-3 text-base leading-relaxed text-white/85 sm:text-lg">
                    Find a boarding house in Sogod that fits your budget and your daily routine.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('renter.search') }}" class="btn btn-lantern btn-lg">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-search"/></svg>
                        Find a place
                    </a>
                    <a href="{{ route('renter.favorites') }}"
                       class="btn btn-lg border border-white/40 text-white hover:bg-white/10">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-heart"/></svg>
                        Your favorites
                    </a>
                </div>
            </div>
        </section>

        {{-- ================= Stats ================= --}}
        <section aria-label="Your activity" class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('renter.applications') }}"
               class="group rounded-2xl border border-line bg-white p-5 transition-shadow hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-file"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" class="ico text-muted transition-transform group-hover:translate-x-0.5"><use href="#l-arrow"/></svg>
                </div>
                <p class="mt-5 text-sm text-muted">Applications</p>
                <p class="font-display mt-0.5 text-3xl font-bold text-bay">{{ $applicationsCount ?? '—' }}</p>
                <p class="mt-1 text-sm text-muted">Track each one from submitted to accepted.</p>
            </a>

            <a href="{{ route('renter.favorites') }}"
               class="group rounded-2xl border border-line bg-white p-5 transition-shadow hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-heart"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" class="ico text-muted transition-transform group-hover:translate-x-0.5"><use href="#l-arrow"/></svg>
                </div>
                <p class="mt-5 text-sm text-muted">Favorites</p>
                <p class="font-display mt-0.5 text-3xl font-bold text-bay">{{ $favoritesCount ?? '—' }}</p>
                <p class="mt-1 text-sm text-muted">Boarding houses you saved to compare.</p>
            </a>

            <a href="{{ route('renter.trust') }}"
               class="group rounded-2xl border border-line bg-white p-5 transition-shadow hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-shield"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" class="ico text-muted transition-transform group-hover:translate-x-0.5"><use href="#l-arrow"/></svg>
                </div>
                <div class="mt-5 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-sm text-muted">Trust score</p>
                        <p class="font-display mt-0.5 text-3xl font-bold text-bay">
                            {{ $hasScore ? number_format($scoreValue, 2) : '—' }}
                        </p>
                    </div>
                    @if ($hasScore)
                        <div class="relative h-16 w-16 flex-none">
                            <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90" aria-hidden="true">
                                <circle cx="60" cy="60" r="52" fill="none" stroke="#E3EBEA" stroke-width="12"/>
                                <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $ringColor }}" stroke-width="12" stroke-linecap="round"
                                        stroke-dasharray="{{ round($circ * $pct / 100, 2) }} {{ round($circ, 2) }}"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <p class="mt-1 text-sm text-muted">Private until you apply to a landlord.</p>
            </a>
        </section>

        {{-- ================= How applying works ================= --}}
        <section aria-labelledby="how-heading">
            <h2 id="how-heading" class="font-display text-xl font-bold text-bay">How applying works</h2>
            <ol class="mt-4 grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['Search and save', 'Filter by budget, room type, and house rules like curfew and cooking. Save the places you like.'],
                    ['Apply to a room', 'Pick the specific room you want and add a message if you like. One open application per room.'],
                    ['Follow the result', 'Your application moves from submitted to viewed to accepted. You can withdraw any time.'],
                ] as $i => [$title, $text])
                    <li class="rounded-2xl border border-line bg-white p-5">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-bay text-sm font-semibold text-white">{{ $i + 1 }}</span>
                        <p class="mt-4 font-semibold text-bay">{{ $title }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </section>
    </div>
@endsection