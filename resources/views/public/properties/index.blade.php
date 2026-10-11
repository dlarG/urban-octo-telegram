@extends('layouts.public')

@section('title', 'Find a place in Sogod')

@section('content')
    @php
        $filterCount = collect([request('gender'), request('min'), request('max'), request('allows_cooking'), request('near')])
            ->filter()->count();
        $pill = 'block rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-bay hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]';
    @endphp

    <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8">

        {{-- ================= Hero + search ================= --}}
        <section class="relative z-20 rounded-3xl bg-bay text-white">
            <div class="absolute inset-0 overflow-hidden rounded-3xl" aria-hidden="true">
                <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70"
                     alt="" class="h-full w-full object-cover" onerror="this.style.visibility='hidden'">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/40"></div>
            </div>

            <div class="relative px-6 py-8 sm:px-10 sm:py-10">
                <div class="max-w-xl">
                    <h1 class="font-display text-2xl font-bold sm:text-3xl">Find a place in Sogod</h1>
                    <p class="mt-3 text-base leading-relaxed text-white/85">
                        {{ $houses->total() }} {{ \Illuminate\Support\Str::plural('boarding house', $houses->total()) }} from landlords checked by an admin.
                    </p>
                </div>

                <form method="GET" action="{{ url()->current() }}" class="relative mt-6"
                      x-data="{ open: {{ $filterCount > 0 ? 'true' : 'false' }} }" @click.outside="open = false">

                    <div class="rounded-xl border border-line bg-white p-2 text-bay shadow-lg">
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative flex-1">
                                <svg viewBox="0 0 24 24" class="ico pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted" style="width:1rem;height:1rem"><use href="#i-search"/></svg>
                                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name"
                                       aria-label="Search by name"
                                       class="h-10 w-full rounded-md border border-line bg-white pl-10 pr-3 text-sm placeholder:text-gray-400 focus:border-[#177E89] focus:outline-none focus:ring-0">
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="open = !open"
                                        class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-md border border-line bg-white px-4 text-sm font-medium text-bay hover:bg-mist sm:flex-none"
                                        :aria-expanded="open.toString()" aria-controls="filterPanel">
                                    Filters
                                    @if ($filterCount > 0)
                                        <span class="grid h-4 min-w-4 place-items-center rounded-full bg-bay px-1 text-[10px] font-semibold text-white">{{ $filterCount }}</span>
                                    @endif
                                </button>
                                <button type="submit" class="inline-flex h-10 flex-1 items-center justify-center rounded-md bg-bay px-5 text-sm font-medium text-white hover:bg-[#08303a] sm:flex-none">Search</button>
                            </div>
                        </div>
                    </div>

                    <div id="filterPanel" x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 top-full z-30 mt-2 max-h-[70vh] overflow-y-auto rounded-2xl border border-line bg-white p-4 text-bay shadow-xl sm:p-5">

                        <div class="grid gap-5 lg:grid-cols-2">
                            <fieldset>
                                <legend class="mb-2 text-[13px] font-medium">Gender policy</legend>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach (['' => 'Any', 'male_only' => 'Male only', 'female_only' => 'Female only', 'mixed' => 'Mixed'] as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="gender" value="{{ $value }}" class="peer sr-only" @checked((string) request('gender') === (string) $value)>
                                            <span class="{{ $pill }}">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend class="mb-2 text-[13px] font-medium">Monthly price</legend>
                                <div class="grid grid-cols-2 gap-2.5">
                                    @foreach (['min' => 'Min', 'max' => 'Max'] as $name => $ph)
                                        <label class="relative block">
                                            <span class="sr-only">{{ $ph }} price</span>
                                            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-8 place-items-center text-xs text-muted" aria-hidden="true">₱</span>
                                            <input type="number" inputmode="numeric" min="0" name="{{ $name }}" value="{{ request($name) }}" placeholder="{{ $ph }}"
                                                   class="h-10 w-full rounded-md border border-line pl-7 text-sm focus:border-[#177E89] focus:outline-none focus:ring-0">
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        <fieldset class="mt-5">
                            <legend class="mb-2 text-[13px] font-medium">House rules</legend>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach (['allows_cooking' => 'Allows cooking', 'near' => 'Near Sogod center (5 km)'] as $name => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked(request($name))>
                                        <span class="{{ $pill }}">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="mt-5 flex justify-end gap-2 border-t border-line pt-4">
                            <a href="{{ url()->current() }}" class="inline-flex h-10 items-center rounded-md border border-line px-4 text-sm font-medium hover:bg-mist">Clear all</a>
                            <button type="submit" class="inline-flex h-10 items-center rounded-md bg-bay px-4 text-sm font-medium text-white hover:bg-[#08303a]">Apply filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        {{-- ================= Results ================= --}}
        <div class="mt-8">
            @if ($houses->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-14 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#i-search"/></svg>
                    </span>
                    <h2 class="font-display mt-4 text-lg font-bold text-bay">No boarding houses match</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted">Try a wider budget or fewer filters.</p>
                    <a href="{{ url()->current() }}" class="mt-5 inline-flex h-11 items-center rounded-md bg-bay px-5 text-sm font-medium text-white hover:bg-[#08303a]">Clear all filters</a>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($houses as $house)
                        @php $cover = $house->primaryImage(); @endphp

                        {{-- One card = one <article>. Only the title link covers the card, so no nested anchors. --}}
                        <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-xl">

                            <div class="relative aspect-[4/3] overflow-hidden bg-mist">
                                @if ($cover)
                                    <img src="{{ Storage::url($cover->path) }}" alt="Photo of {{ $house->name }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#9BB5B3]">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:2.25rem;height:2.25rem;stroke-width:1.25"><use href="#i-image"/></svg>
                                    </div>
                                @endif

                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-semibold text-bay shadow-sm">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#i-lock"/></svg>
                                    Details hidden
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-4">
                                <h2 class="font-display text-base font-bold leading-snug text-bay">
                                    <a href="{{ route('properties.show', $house) }}"
                                       class="after:absolute after:inset-0 after:content-[''] focus-visible:after:rounded-2xl">{{ $house->name }}</a>
                                </h2>
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-muted">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#i-pin"/></svg>
                                    Location hidden
                                </p>
                                <p class="mt-3 text-sm text-muted">₱??? <span class="text-xs">/ month</span></p>
                            </div>

                            <div class="relative z-10 mt-auto flex items-center justify-between border-t border-line px-4 py-2.5 text-xs text-muted">
                                <span>Sign in to see price and location</span>
                                <a href="{{ route('login') }}" class="font-semibold text-sea hover:underline">Sign in</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">{{ $houses->links() }}</div>
            @endif
        </div>
    </div>
@endsection