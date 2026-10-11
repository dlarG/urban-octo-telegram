@extends('layouts.public')

@section('title', $house->name)

@section('content')
    @php
        $isGuest = ! auth()->check();

        $genderLabels = ['male_only' => 'Male only', 'female_only' => 'Female only', 'mixed' => 'Mixed'];
        $gv           = $house->gender_policy instanceof \BackedEnum ? $house->gender_policy->value : (string) $house->gender_policy;
        $genderText   = $genderLabels[$gv] ?? ucfirst(str_replace('_', ' ', $gv));

        $curfew = null;
        if ($house->curfew_time) {
            try { $curfew = \Illuminate\Support\Carbon::parse($house->curfew_time)->format('g:i A'); }
            catch (\Throwable $e) { $curfew = (string) $house->curfew_time; }
        }

        $cover = $isGuest ? null : $house->primaryImage();
        $heroImg = $cover
            ? Storage::url($cover->path)
            : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70';
    @endphp

    <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8">

        {{-- ================= Hero ================= --}}
        <section class="relative isolate overflow-hidden rounded-3xl bg-bay text-white">
            <img src="{{ $heroImg }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/40"></div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                <a href="{{ route('properties.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-white/80 hover:text-white">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#i-back"/></svg>
                    All boarding houses
                </a>

                <div class="mt-4 max-w-2xl">
                    <h1 class="font-display text-2xl font-bold sm:text-4xl">{{ $house->name }}</h1>

                    @if ($isGuest)
                        <p class="mt-3 flex items-center gap-1.5 text-base text-white/85">
                            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#i-lock"/></svg>
                            Location is visible after you sign in
                        </p>
                    @else
                        <p class="mt-3 flex items-start gap-1.5 text-base text-white/85">
                            <svg viewBox="0 0 24 24" class="ico mt-1" style="width:1rem;height:1rem"><use href="#i-pin"/></svg>
                            <span>{{ $house->address_line }}, {{ $house->barangay }}, {{ $house->city }}, {{ $house->province }}</span>
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-users"/></svg>{{ $genderText }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-clock"/></svg>{{ $curfew ? 'Curfew ' . $curfew : 'No curfew' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-flame"/></svg>{{ $house->allows_cooking ? 'Cooking allowed' : 'No cooking' }}
                            </span>
                            @if ($house->is_sub_metered)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-bolt"/></svg>Sub-metered
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Messages --}}
        @if (session('status'))
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#CDE8E5] bg-[#E6F4F2] px-4 py-3 text-[13px] text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1rem;height:1rem"><use href="#i-check"/></svg>
                <p class="font-medium">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 flex items-start gap-3 rounded-xl bg-[#FBEFEE] px-4 py-3 text-[13px] text-[#A32A2A]" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg>
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        @if ($isGuest)
            {{-- ================= Guest: locked state (no private data is rendered) ================= --}}
            <section class="mt-8 rounded-2xl border border-line bg-white px-6 py-12 text-center sm:py-16">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#i-lock"/></svg>
                </span>
                <h2 class="font-display mt-4 text-xl font-bold text-bay">Sign in to see the full listing</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-muted">
                    Photos, prices, amenities, house rules and room details are visible to registered users.
                </p>
                <div class="mt-6 flex flex-col justify-center gap-2 sm:flex-row">
                    <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center rounded-md bg-bay px-6 text-sm font-medium text-white hover:bg-[#08303a]">Log in</a>
                    <a href="{{ route('register') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-line bg-white px-6 text-sm font-medium text-bay hover:bg-mist">Create account</a>
                </div>
            </section>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-3">

                {{-- ================= Main column ================= --}}
                <div class="space-y-10 lg:col-span-2">

                    @if ($house->description)
                        <section aria-labelledby="about-heading">
                            <h2 id="about-heading" class="font-display text-xl font-bold text-bay">About this place</h2>
                            <p class="mt-3 whitespace-pre-line leading-relaxed text-muted">{{ $house->description }}</p>
                        </section>
                    @endif

                    <section aria-labelledby="rooms-heading">
                        <h2 id="rooms-heading" class="font-display text-xl font-bold text-bay">Available rooms</h2>

                        @if ($house->rooms->isEmpty())
                            <div class="mt-4 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-12 text-center">
                                <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#i-bed"/></svg>
                                </span>
                                <p class="mt-4 font-semibold text-bay">No rooms available right now</p>
                                <p class="mx-auto mt-1 max-w-sm text-sm text-muted">Check back later or look at other boarding houses.</p>
                            </div>
                        @else
                            <ul class="mt-4 space-y-4">
                                @foreach ($house->rooms as $room)
                                    @php $roomCover = $room->primaryImage(); @endphp
                                    <li class="flex flex-col overflow-hidden rounded-2xl border border-line bg-white sm:flex-row">

                                        <div class="relative aspect-[16/9] bg-mist sm:aspect-auto sm:w-44 sm:flex-none">
                                            @if ($roomCover)
                                                <img src="{{ Storage::url($roomCover->path) }}" alt="Photo of {{ $room->room_label }}" loading="lazy" class="h-full w-full object-cover">
                                            @else
                                                <div class="grid h-full min-h-[7rem] w-full place-items-center text-[#9BB5B3]">
                                                    <svg viewBox="0 0 24 24" class="ico" style="width:2rem;height:2rem;stroke-width:1.25"><use href="#i-image"/></svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col p-5">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <h3 class="font-display text-lg font-bold leading-snug text-bay">{{ $room->room_label }}</h3>
                                                    <p class="mt-1 text-sm text-muted">
                                                        {{ ucfirst($room->room_type->value) }} · Capacity {{ $room->capacity }}
                                                        @if ($room->has_own_bathroom) · Own bathroom @endif
                                                        @if ($room->has_aircon) · Aircon @endif
                                                    </p>
                                                </div>
                                                <p class="flex-none text-right">
                                                    <span class="font-display text-xl font-bold text-bay">&#8369;{{ number_format($room->base_price_monthly, 2) }}</span>
                                                    <span class="block text-xs text-muted">per month</span>
                                                </p>
                                            </div>

                                            <div class="mt-4 border-t border-line pt-4">
                                                @if (auth()->user()->isRenter())
                                                    @php
                                                        $renterHasActiveTenancy = auth()->user()->hasActiveTenancy();
                                                        $alreadyApplied         = $room->hasBlockingApplicationFrom(auth()->id());
                                                    @endphp

                                                    @if ($renterHasActiveTenancy)
                                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E3EEF6] px-3 py-1 text-xs font-semibold text-[#1F5F8B]">
                                                            You already have an active tenancy
                                                        </span>
                                                    @elseif ($alreadyApplied)
                                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FBF1DC] px-3 py-1 text-xs font-semibold text-[#8A5F10]">
                                                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-clock"/></svg>
                                                            Application pending
                                                        </span>
                                                    @else
                                                        <form method="POST" action="{{ route('renter.applications.store', $room) }}" class="flex flex-col gap-2 sm:flex-row">
                                                            @csrf
                                                            <label for="message-{{ $room->id }}" class="sr-only">Short message (optional)</label>
                                                            <input id="message-{{ $room->id }}" type="text" name="message" placeholder="Short message (optional)"
                                                                   class="h-10 flex-1 rounded-md border border-line bg-white px-3 text-sm placeholder:text-gray-400 focus:border-[#177E89] focus:outline-none focus:ring-0">
                                                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-bay px-5 text-sm font-medium text-white hover:bg-[#08303a]">
                                                                Apply
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <p class="text-xs text-muted">Only renters can apply.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                </div>

                {{-- ================= Side column ================= --}}
                <aside class="space-y-6 lg:col-span-1">
                    <section class="rounded-2xl border border-line bg-white p-6 lg:sticky lg:top-24" aria-labelledby="rules-heading">
                        <h2 id="rules-heading" class="font-display text-lg font-bold text-bay">House rules</h2>
                        <dl class="mt-4 divide-y divide-[#D9E3E2] text-sm">
                            <div class="flex items-center justify-between gap-3 py-2.5">
                                <dt class="text-muted">Gender policy</dt><dd class="font-medium text-bay">{{ $genderText }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-2.5">
                                <dt class="text-muted">Curfew</dt><dd class="font-medium text-bay">{{ $curfew ?: 'None' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-2.5">
                                <dt class="text-muted">Cooking</dt><dd class="font-medium text-bay">{{ $house->allows_cooking ? 'Allowed' : 'Not allowed' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-2.5">
                                <dt class="text-muted">Sub-metered</dt><dd class="font-medium text-bay">{{ $house->is_sub_metered ? 'Yes' : 'No' }}</dd>
                            </div>
                            @if ($house->landlord?->landlordProfile)
                                <div class="flex items-center justify-between gap-3 py-2.5">
                                    <dt class="text-muted">Listed by</dt><dd class="text-right font-medium text-bay">{{ $house->landlord->landlordProfile->business_name }}</dd>
                                </div>
                            @endif
                        </dl>

                        <h2 class="font-display mt-6 text-lg font-bold text-bay">Amenities</h2>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @forelse ($house->amenities as $a)
                                <span class="inline-flex items-center rounded-full bg-mist px-2.5 py-1 text-xs font-medium text-bay">{{ $a->name }}</span>
                            @empty
                                <span class="text-sm text-muted">None listed</span>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        @endif
    </div>
@endsection