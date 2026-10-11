@extends('layouts.renter')

@section('title', 'Favorites')


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
    @endphp
    {{-- Renders a line icon for an amenity.
     Usage: @include('renter._amenity-icon', ['key' => $amenity->icon_key, 'name' => $amenity->name])
     Matches on icon_key first, then the amenity name. Unknown amenities get a check mark. --}}
    @include('renter.search._amenity-icon')

    <div class="mx-auto max-w-7xl">
        @include('partials.renter-profile-banner')
        {{-- ================= Header ================= --}}
        <section class="relative isolate overflow-hidden rounded-3xl bg-bay text-white">
            <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70"
                alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/40"></div>

            <div class="flex flex-col gap-5 px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-10 sm:py-10">
                <div class="max-w-xl">
                    <h1 class="font-display text-2xl font-bold sm:text-3xl">Favorites</h1>
                    <p class="mt-3 text-base leading-relaxed text-white/85">
                        Boarding houses you saved for later. You can remove a place from your favorites by tapping the heart icon on its card.
                    </p>
                </div>

                @if (! $favorites->isEmpty())
                    <a href="{{ route('renter.search') }}" class="btn btn-lantern self-start sm:self-auto">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-search"/></svg>
                        Find more places
                    </a>
                @endif
            </div>
        </section>

        {{-- ================= Content ================= --}}
        <div class="mt-8">
            @if ($favorites->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-14 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#l-heart"/></svg>
                    </span>
                    <h2 class="font-display mt-4 text-lg font-bold text-bay">No favorites yet</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted">
                        Tap the heart on a boarding house to save it here, so you can compare places side by side.
                    </p>
                    <a href="{{ route('renter.search') }}" class="btn btn-primary btn-lg mt-5 text-sm">Find a place</a>
                </div>
            @else
                <p class="text-[13px] text-muted">
                    <span class="font-semibold text-bay">{{ $favorites->total() }}</span>
                    saved {{ \Illuminate\Support\Str::plural('boarding house', $favorites->total()) }}
                </p>

                <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($favorites as $favorite)
                        @php
                            $house       = $favorite->boardingHouse;
                            $cover       = $house->primaryImage();
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

                                {{-- Remove from favorites --}}
                                <form method="POST" action="{{ route('renter.favorites.toggle', $house) }}" class="absolute right-3 top-3 z-10">
                                    @csrf
                                    <button type="submit"
                                            class="grid h-9 w-9 place-items-center rounded-full bg-[#0B3C49] text-white shadow-sm transition-colors hover:bg-[#A32A2A]"
                                            aria-label="Remove {{ $house->name }} from favorites" title="Remove from favorites">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:1.05rem;height:1.05rem;fill:currentColor;"><use href="#l-heart"/></svg>
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
                                    @if (isset($house->rooms_count))
                                        <span class="font-semibold text-bay">{{ $house->rooms_count }}</span>
                                        {{ \Illuminate\Support\Str::plural('room', $house->rooms_count) }} available
                                    @else
                                        Saved {{ $favorite->created_at->diffForHumans() }}
                                    @endif
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

                <div class="mt-10">{{ $favorites->links() }}</div>
            @endif
        </div>
    </div>
@endsection