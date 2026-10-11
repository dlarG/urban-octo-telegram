@extends('layouts.renter')

@section('title', 'Find a place')

@section('content')
    @php
        $genderLabels = ['male_only' => 'Male only', 'female_only' => 'Female only', 'mixed' => 'Mixed'];
        $policyText   = function ($p) use ($genderLabels) {
            $v = $p instanceof \BackedEnum ? $p->value : (string) $p;
            return $genderLabels[$v] ?? ucfirst(str_replace('_', ' ', $v));
        };
        $curfewText = function ($t) {
            if (! $t) return null;
            try { return \Illuminate\Support\Carbon::parse($t)->format('g:i A'); }
            catch (\Throwable $e) { return (string) $t; }
        };

        // Active filters (use filled() so empty form fields don't count)
        $selectedAmenities = array_filter((array) request('amenities', []));
        $chips = [];
        if (request()->filled('q'))            $chips[] = ['"' . request('q') . '"', ['q']];
        if (request()->filled('gender'))       $chips[] = [$genderLabels[request('gender')] ?? request('gender'), ['gender']];
        if (request()->filled('min_price'))    $chips[] = ['From ₱' . number_format((float) request('min_price')), ['min_price']];
        if (request()->filled('max_price'))    $chips[] = ['Up to ₱' . number_format((float) request('max_price')), ['max_price']];
        if (request()->filled('radius'))       $chips[] = ['Within ' . (int) request('radius') . ' km', ['radius']];
        if (request()->filled('allows_cooking')) $chips[] = ['Allows cooking', ['allows_cooking']];
        if (request()->filled('sub_metered'))  $chips[] = ['Sub-metered', ['sub_metered']];
        if (count($selectedAmenities))         $chips[] = [count($selectedAmenities) . ' ' . \Illuminate\Support\Str::plural('amenity', count($selectedAmenities)), ['amenities']];

        $filterCount = count($chips) - (request()->filled('q') ? 1 : 0);
    @endphp

    <div class="mx-auto max-w-7xl">

        {{-- ================= Header ================= --}}
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-bold text-bay sm:text-3xl">Find a place</h1>
                <p class="mt-1 text-sm text-muted">Boarding houses in Sogod from landlords checked by an admin.</p>
            </div>
        </div>

        {{-- Active tenancy notice --}}
        @if ($hasActiveTenancy)
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-line bg-mist px-4 py-3 text-[13px] text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1rem;height:1rem"><use href="#l-info"/></svg>
                <div>
                    <p class="font-semibold">You already have an active tenancy</p>
                    <p class="mt-0.5 text-muted">You can browse listings, but you cannot apply to another room until your current tenancy ends.</p>
                    @if ($activeTenancy)
                        <p class="mt-1 text-muted">Currently at <span class="font-medium text-bay">{{ $activeTenancy->room->room_label }}</span>, {{ $activeTenancy->room->boardingHouse->name }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg>
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- ================= Search + filters ================= --}}
        <form method="GET" action="{{ route('renter.search') }}"
            class="relative mt-5"
            x-data="{ open: {{ $filterCount > 0 ? 'true' : 'false' }} }"
            @click.outside="open = false">

            {{-- Search bar (stays in flow, compact) --}}
            <div class="rounded-xl border border-line bg-white p-2 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row">

                    {{-- Search input with properly-sized icon --}}
                    <div class="relative flex-1">

                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="Search by name or barangay"
                            aria-label="Search by name or barangay"
                            class="field h-10 w-full pl-10 pr-3 text-sm">
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="open = !open"
                                class="btn btn-secondary h-10 flex-1 px-4 text-sm sm:flex-none"
                                :aria-expanded="open.toString()" aria-controls="filterPanel">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            <span>Filters</span>
                            @if ($filterCount > 0)
                                <span class="grid h-4 min-w-4 place-items-center rounded-full bg-bay px-1 text-[10px] font-semibold text-white">{{ $filterCount }}</span>
                            @endif
                        </button>
                        <button type="submit" class="btn btn-primary h-10 flex-1 px-5 text-sm sm:flex-none">Search</button>
                    </div>
                </div>
            </div>

            {{-- Filter panel: absolutely positioned, floats over results --}}
            <div id="filterPanel"
                 x-show="open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.99]"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="absolute left-0 right-0 top-full z-30 mt-2 max-h-[70vh] overflow-y-auto rounded-2xl border border-line bg-white p-4 shadow-xl sm:p-5">

                <div class="grid gap-5 lg:grid-cols-3">
                    {{-- Gender policy --}}
                    <fieldset>
                        <legend class="mb-2 text-[13px] font-medium text-bay">Gender policy</legend>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (['' => 'Any', 'male_only' => 'Male only', 'female_only' => 'Female only', 'mixed' => 'Mixed'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $value }}" class="peer sr-only" @checked((string) request('gender') === (string) $value)>
                                    <span class="block rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-bay hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Price --}}
                    <fieldset>
                        <legend class="mb-2 text-[13px] font-medium text-bay">Monthly price</legend>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="relative block">
                                <span class="sr-only">Minimum price</span>
                                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-8 place-items-center text-xs text-muted" aria-hidden="true">₱</span>
                                <input type="number" inputmode="numeric" min="0" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="field !h-10 pl-7 text-sm">
                            </label>
                            <label class="relative block">
                                <span class="sr-only">Maximum price</span>
                                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-8 place-items-center text-xs text-muted" aria-hidden="true">₱</span>
                                <input type="number" inputmode="numeric" min="0" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="field !h-10 pl-7 text-sm">
                            </label>
                        </div>
                    </fieldset>

                    {{-- Distance --}}
                    <div>
                        <label for="radius" class="mb-2 block text-[13px] font-medium text-bay">Distance from Sogod center</label>
                        <select id="radius" name="radius" class="field !h-10 text-sm">
                            <option value="">Any distance</option>
                            @foreach ([1, 3, 5, 10] as $km)
                                <option value="{{ $km }}" @selected((int) request('radius') === $km)>Within {{ $km }} km</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- House rules --}}
                <fieldset class="mt-5">
                    <legend class="mb-2 text-[13px] font-medium text-bay">House rules</legend>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ([['allows_cooking', 'Allows cooking', 'flame'], ['sub_metered', 'Sub-metered utilities', 'bolt']] as [$name, $label, $icon])
                            <label class="cursor-pointer">
                                <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked(request()->filled($name))>
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-bay hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#am-{{ $icon }}"/></svg>{{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Amenities --}}
                <fieldset class="mt-5">
                    <legend class="mb-2 text-[13px] font-medium text-bay">Amenities</legend>
                    <div class="space-y-3">
                        @foreach ($amenitiesForFilter as $category => $items)
                            <div>
                                <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-muted">{{ $category }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($items as $a)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="amenities[]" value="{{ $a->id }}" class="peer sr-only" @checked(in_array($a->id, $selectedAmenities))>
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-bay hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                                @include('renter.search._amenity-icon', ['key' => $a->icon_key ?? '', 'name' => $a->name])
                                                {{ $a->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>

                <div class="mt-5 flex justify-end gap-2 border-t border-line pt-4">
                    <a href="{{ route('renter.search') }}" class="btn btn-secondary text-sm">Clear all</a>
                    <button type="submit" class="btn btn-primary text-sm">Apply filters</button>
                </div>
            </div>
        </form>

        {{-- Applied filter chips --}}
        @if (count($chips))
            <div class="mt-4 flex flex-wrap items-center gap-2" aria-label="Applied filters">
                @foreach ($chips as [$label, $keys])
                    <a href="{{ request()->fullUrlWithoutQuery(array_merge($keys, ['page'])) }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-mist px-2.5 py-1 text-xs font-medium text-bay hover:bg-[#E0EAE8]"
                       aria-label="Remove filter: {{ $label }}">
                        {{ $label }}
                        <svg viewBox="0 0 24 24" class="ico" style="width:.75rem;height:.75rem"><use href="#l-x"/></svg>
                    </a>
                @endforeach
                <a href="{{ route('renter.search') }}" class="ml-1 text-xs font-medium text-sea hover:underline">Clear all</a>
            </div>
        @endif

        {{-- ================= Results ================= --}}
        <div class="mt-8">
            <p class="text-[13px] text-muted">
                <span class="font-semibold text-bay">{{ $houses->total() }}</span>
                {{ \Illuminate\Support\Str::plural('boarding house', $houses->total()) }}
                @if (request()->filled('q')) matching "{{ request('q') }}" @endif
            </p>

            @if ($houses->isEmpty())
                <div class="mt-4 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-14 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#l-search"/></svg>
                    </span>
                    <h2 class="font-display mt-4 text-lg font-bold text-bay">No boarding houses match</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted">Try a wider budget, fewer amenities, or a larger distance.</p>
                    <a href="{{ route('renter.search') }}" class="btn btn-primary btn-lg mt-5 text-sm">Clear all filters</a>
                </div>
            @else
                <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($houses as $house)
                        @php
                            $cover       = $house->primaryImage();
                            $isFavorited = isset($favoriteIds)
                                ? in_array($house->id, $favoriteIds)
                                : auth()->user()->favorites()->where('boarding_house_id', $house->id)->exists();
                            $amenityList = $house->relationLoaded('amenities') ? $house->amenities : collect();
                            $shown       = $amenityList->take(4);
                            $extra       = $amenityList->count() - $shown->count();
                            $curfew      = $curfewText($house->curfew_time);
                        @endphp

                        <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-xl">

                            {{-- Photo --}}
                            <div class="relative aspect-[4/3] overflow-hidden bg-mist">
                                @if ($cover)
                                    <img src="{{ Storage::url($cover->path) }}" alt="Photo of {{ $house->name }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#9BB5B3]">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:2.25rem;height:2.25rem;stroke-width:1.25"><use href="#l-image"/></svg>
                                    </div>
                                @endif

                                <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-semibold text-bay shadow-sm">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#am-users"/></svg>
                                    {{ $policyText($house->gender_policy) }}
                                </span>

                                {{-- Favorite --}}
                                <form method="POST" action="{{ route('renter.favorites.toggle', $house) }}" class="absolute right-3 top-3 z-10">
                                    @csrf
                                    <button type="submit"
                                            class="grid h-9 w-9 place-items-center rounded-full shadow-sm transition-colors {{ $isFavorited ? 'bg-[#0B3C49] text-white' : 'bg-white/95 text-[#0B3C49] hover:bg-white' }}"
                                            aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}" aria-pressed="{{ $isFavorited ? 'true' : 'false' }}">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:1.05rem;height:1.05rem;{{ $isFavorited ? 'fill:currentColor;' : '' }}"><use href="#l-heart"/></svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Body --}}
                            <div class="flex flex-1 flex-col p-4">
                                <h2 class="font-display text-base font-bold leading-snug text-bay">
                                    <a href="{{ route('renter.properties.show', $house) }}"
                                       class="after:absolute after:inset-0 after:content-[''] focus-visible:after:rounded-2xl">{{ $house->name }}</a>
                                </h2>
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-muted">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-pin"/></svg>
                                    <span class="truncate">{{ $house->barangay }}, {{ $house->city }}</span>
                                </p>

                                <p class="mt-3">
                                    @if ($house->price_range_label)
                                        <span class="font-display text-lg font-bold text-bay">{{ $house->price_range_label }}</span>
                                        <span class="text-xs text-muted">/ month</span>
                                    @else
                                        <span class="text-xs font-medium text-muted">No rooms listed yet</span>
                                    @endif
                                </p>

                                {{-- Amenities --}}
                                @if ($shown->isNotEmpty())
                                    <ul class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach ($shown as $a)
                                            <li class="inline-flex items-center gap-1 rounded-full bg-mist px-2 py-0.5 text-[11px] font-medium text-bay">
                                                @include('renter._amenity-icon', ['key' => $a->icon_key ?? '', 'name' => $a->name, 'class' => 'text-sea', 'style' => 'width:.8rem;height:.8rem'])
                                                {{ $a->name }}
                                            </li>
                                        @endforeach
                                        @if ($extra > 0)
                                            <li class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[11px] font-medium text-muted">+{{ $extra }} more</li>
                                        @endif
                                    </ul>
                                @endif

                                {{-- House rules --}}
                                <ul class="mt-3 flex flex-wrap gap-x-3 gap-y-1 border-t border-line pt-3 text-xs text-muted">
                                    <li class="flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#am-clock"/></svg>
                                        {{ $curfew ? 'Curfew ' . $curfew : 'No curfew' }}
                                    </li>
                                    <li class="flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#am-flame"/></svg>
                                        {{ $house->allows_cooking ? 'Cooking allowed' : 'No cooking' }}
                                    </li>
                                    @if ($house->is_sub_metered)
                                        <li class="flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#am-bolt"/></svg>
                                            Sub-metered
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Footer --}}
                            <div class="relative z-10 mt-auto flex items-center justify-between border-t border-line px-4 py-2.5">
                                <span class="text-xs text-muted">
                                    <span class="font-semibold text-bay">{{ $house->rooms_count }}</span>
                                    {{ \Illuminate\Support\Str::plural('room', $house->rooms_count) }} available
                                </span>
                                @include('partials.location-modal', [
                                    'modalId' => 'loc-' . $house->id,
                                    'lat'     => $house->lat,
                                    'lng'     => $house->lng,
                                    'title'   => $house->name,
                                ])
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">{{ $houses->links() }}</div>
            @endif
        </div>
    </div>
@endsection