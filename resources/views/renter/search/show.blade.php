@extends('layouts.renter')

@section('title', $boarding_house->name)

@section('content')
    @php
        $house   = $boarding_house;
        $images  = $house->propertyImages;
        $count   = $images->count();
        $gallery = $images->map(fn ($i) => Storage::url($i->path))->values();

        $policyValue = $house->gender_policy instanceof \BackedEnum ? $house->gender_policy->value : (string) $house->gender_policy;
        $policyText  = ['male_only' => 'Male only', 'female_only' => 'Female only', 'mixed' => 'Mixed'][$policyValue]
                       ?? ucfirst(str_replace('_', ' ', $policyValue));

        $curfew = null;
        if ($house->curfew_time) {
            try { $curfew = \Illuminate\Support\Carbon::parse($house->curfew_time)->format('g:i A'); }
            catch (\Throwable $e) { $curfew = (string) $house->curfew_time; }
        }

        $water = $house->water_supply_rating ?? null;
        $water = $water instanceof \BackedEnum ? ucfirst((string) $water->value) : $water;

        $minPrice = $house->rooms->min('base_price_monthly');
        $maxPrice = $house->rooms->max('base_price_monthly');
        $fmt      = fn ($n) => number_format((float) $n, fmod((float) $n, 1) == 0.0 ? 0 : 2);

        $groupedAmenities = $house->amenities->groupBy(function ($a) {
            $c = $a->category instanceof \BackedEnum ? $a->category->value : $a->category;
            return $c ? ucfirst((string) $c) : 'Other';
        });

        // Gallery layout depends on how many photos exist
        $thumbCount = match (true) { $count <= 1 => 0, $count === 2 => 1, $count <= 4 => 2, default => 4 };
        $mainClass  = $count <= 1 ? 'md:col-span-4 md:row-span-2' : 'md:col-span-2 md:row-span-2';
        $thumbClass = $count === 2 ? 'md:col-span-2 md:row-span-2' : ($count <= 4 ? 'md:col-span-2' : '');

        $rules = [
            ['users', 'Gender policy', $policyText],
            ['flame', 'Cooking', $house->allows_cooking ? 'Allowed' : 'Not allowed'],
            ['clock', 'Curfew', $curfew ?: 'None'],
            ['bolt', 'Utilities', $house->is_sub_metered ? 'Sub-metered' : 'Not sub-metered'],
        ];
        if ($water !== null && $water !== '') $rules[] = ['droplet', 'Water supply', $water];
    @endphp

    <div class="mx-auto max-w-6xl">

        <a href="{{ route('renter.search') }}" class="inline-flex items-center gap-1 text-xs font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-back"/></svg>
            Back to search
        </a>

        @if ($hasActiveTenancy)
            <div class="mt-4 flex items-start gap-3 rounded-xl border border-line bg-mist px-4 py-3 text-[13px] text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1rem;height:1rem"><use href="#l-info"/></svg>
                <div>
                    <p class="font-semibold">You already have an active tenancy</p>
                    <p class="mt-0.5 text-muted">You can browse, but you cannot apply to another room until your current tenancy ends.</p>
                    @if ($activeTenancy)
                        <p class="mt-1 text-muted">Currently at <span class="font-medium text-bay">{{ $activeTenancy->room->room_label }}</span>, {{ $activeTenancy->room->boardingHouse->name }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg>
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- ================= Title row ================= --}}
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="font-display text-2xl font-bold text-bay sm:text-3xl">{{ $house->name }}</h1>
                <p class="mt-2 flex items-start gap-1.5 text-sm text-muted">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-pin"/></svg>
                    <span>{{ $house->address_line }}, {{ $house->barangay }}, {{ $house->city }}, {{ $house->province }}</span>
                </p>
            </div>

            <div class="flex flex-none items-center gap-2">
                @include('partials.location-modal', [
                    'modalId' => 'loc-house-' . $house->id,
                    'lat'     => $house->lat,
                    'lng'     => $house->lng,
                    'title'   => $house->name,
                ])

                <form method="POST" action="{{ route('renter.favorites.toggle', $house) }}">
                    @csrf
                    <button type="submit" aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                            class="btn {{ $isFavorited ? 'btn-primary' : 'btn-secondary' }} !h-10 text-sm">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem;{{ $isFavorited ? 'fill:currentColor;' : '' }}"><use href="#l-heart"/></svg>
                        {{ $isFavorited ? 'Saved' : 'Save' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= Gallery ================= --}}
        <div class="mt-5">
            @if ($count > 0)
                <div class="grid gap-2 overflow-hidden rounded-3xl md:h-[24rem] md:grid-cols-4 md:grid-rows-2">
                    <button type="button" data-gallery-open="0"
                            class="group relative aspect-[4/3] overflow-hidden bg-mist md:aspect-auto md:h-full {{ $mainClass }}"
                            aria-label="Open photo 1 of {{ $count }}">
                        <img src="{{ $gallery[0] }}" alt="{{ $house->name }}, main photo" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" fetchpriority="high">
                        @if ($count > 1)
                            <span class="absolute bottom-3 right-3 inline-flex items-center gap-1.5 rounded-lg bg-white/95 px-2.5 py-1.5 text-xs font-medium text-bay shadow-sm md:hidden">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-image"/></svg>
                                All {{ $count }} photos
                            </span>
                        @endif
                    </button>

                    @for ($i = 1; $i <= $thumbCount; $i++)
                        <button type="button" data-gallery-open="{{ $i }}"
                                class="group relative hidden overflow-hidden bg-mist md:block {{ $thumbClass }}"
                                aria-label="Open photo {{ $i + 1 }} of {{ $count }}">
                            <img src="{{ $gallery[$i] }}" alt="{{ $house->name }}, photo {{ $i + 1 }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none">
                            @if ($i === $thumbCount && $count > $thumbCount + 1)
                                <span class="absolute inset-0 grid place-items-center bg-[#0B3C49]/60 text-xs font-semibold text-white">
                                    Show all {{ $count }} photos
                                </span>
                            @endif
                        </button>
                    @endfor
                </div>
            @else
                <div class="grid aspect-[16/6] place-items-center rounded-3xl bg-mist text-center text-muted">
                    <div>
                        <svg viewBox="0 0 24 24" class="ico mx-auto text-[#9BB5B3]" style="width:2.25rem;height:2.25rem;stroke-width:1.25"><use href="#l-image"/></svg>
                        <p class="mt-2 text-xs">The landlord has not added photos yet.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ================= Content + sidebar ================= --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">

                {{-- House rules at a glance --}}
                <section aria-labelledby="rules-heading">
                    <h2 id="rules-heading" class="font-display text-lg font-bold text-bay">House rules at a glance</h2>
                    <dl class="mt-3 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                        @foreach ($rules as [$icon, $label, $value])
                            <div class="rounded-2xl border border-line bg-white p-3.5">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#am-{{ $icon }}"/></svg>
                                </span>
                                <dt class="mt-2.5 text-[11px] text-muted">{{ $label }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-bay">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- About --}}
                @if ($house->description)
                    <section aria-labelledby="about-heading">
                        <h2 id="about-heading" class="font-display text-lg font-bold text-bay">About this place</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-[#2B4047]">{{ $house->description }}</p>
                    </section>
                @endif

                {{-- Amenities --}}
                <section aria-labelledby="amenities-heading">
                    <h2 id="amenities-heading" class="font-display text-lg font-bold text-bay">Amenities</h2>
                    @if ($house->amenities->isEmpty())
                        <p class="mt-3 text-sm text-muted">No amenities listed.</p>
                    @else
                        <div class="mt-3 space-y-5">
                            @foreach ($groupedAmenities as $category => $items)
                                <div>
                                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-muted">{{ $category }}</p>
                                    <ul class="grid gap-2 sm:grid-cols-2">
                                        @foreach ($items as $a)
                                            <li class="flex items-center gap-3 rounded-xl border border-line bg-white px-3.5 py-2.5">
                                                <span class="grid h-8 w-8 flex-none place-items-center rounded-lg bg-mist text-sea">
                                                    @include('renter.search._amenity-icon', ['key' => $a->icon_key ?? '', 'name' => $a->name, 'style' => 'width:1rem;height:1rem'])
                                                </span>
                                                <span class="text-sm font-medium text-bay">{{ $a->name }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Rooms --}}
                <section id="rooms" class="scroll-mt-24" aria-labelledby="rooms-heading">
                    <h2 id="rooms-heading" class="font-display text-lg font-bold text-bay">
                        Available rooms
                        <span class="font-sans text-sm font-normal text-muted">({{ $house->rooms->count() }})</span>
                    </h2>

                    @if ($house->rooms->isEmpty())
                        <div class="mt-4 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-10 text-center text-sm text-muted">
                            No rooms are available right now.
                        </div>
                    @else
                        <ul class="mt-4 space-y-3">
                            @foreach ($house->rooms as $room)
                                @php
                                    $cover          = $room->primaryImage();
                                    $alreadyApplied = in_array($room->id, $appliedRoomIds);
                                @endphp
                                <li class="overflow-hidden rounded-2xl border border-line bg-white">
                                    <div class="flex flex-col sm:flex-row">
                                        <div class="aspect-[16/9] w-full flex-none bg-mist sm:aspect-auto sm:w-44">
                                            @if ($cover)
                                                <img src="{{ Storage::url($cover->path) }}" alt="Photo of {{ $room->room_label }}" loading="lazy" class="h-full w-full object-cover">
                                            @else
                                                <div class="grid h-full min-h-[7rem] w-full place-items-center text-[#9BB5B3]">
                                                    <svg viewBox="0 0 24 24" class="ico" style="width:1.75rem;height:1.75rem;stroke-width:1.25"><use href="#l-bed"/></svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="font-display text-base font-bold text-bay">{{ $room->room_label }}</h3>
                                                    <ul class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted">
                                                        <li class="flex items-center gap-1">
                                                            <svg viewBox="0 0 24 24" class="ico text-sea" style="width:.9rem;height:.9rem"><use href="#l-{{ $room->room_type->value === 'shared' ? 'users' : 'user' }}"/></svg>
                                                            {{ ucfirst($room->room_type->value) }}, fits {{ $room->capacity }}
                                                        </li>
                                                        @if ($room->has_own_bathroom)
                                                            <li class="flex items-center gap-1"><svg viewBox="0 0 24 24" class="ico text-sea" style="width:.9rem;height:.9rem"><use href="#am-droplet"/></svg>Own bathroom</li>
                                                        @endif
                                                        @if ($room->has_aircon)
                                                            <li class="flex items-center gap-1"><svg viewBox="0 0 24 24" class="ico text-sea" style="width:.9rem;height:.9rem"><use href="#am-wind"/></svg>Aircon</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                <p class="flex-none text-right">
                                                    <span class="font-display text-lg font-bold text-bay">₱{{ $fmt($room->base_price_monthly) }}</span>
                                                    <span class="block text-[11px] text-muted">per month</span>
                                                </p>
                                            </div>

                                            <div class="mt-3 border-t border-line pt-3 sm:mt-auto">
                                                @if ($hasActiveTenancy)
                                                    <p class="inline-flex items-center gap-1.5 rounded-full bg-mist px-2.5 py-1 text-xs font-medium text-bay">
                                                        <svg viewBox="0 0 24 24" class="ico text-sea" style="width:.9rem;height:.9rem"><use href="#l-info"/></svg>
                                                        You already have an active tenancy
                                                    </p>
                                                @elseif ($alreadyApplied)
                                                    <p class="inline-flex items-center gap-1.5 rounded-full bg-[#E1F2EC] px-2.5 py-1 text-xs font-medium text-[#0F6B5A]">
                                                        <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-check-circle"/></svg>
                                                        Application sent
                                                    </p>
                                                @else
                                                    <form method="POST" action="{{ route('renter.applications.store', $room) }}" class="flex flex-col gap-2 sm:flex-row">
                                                        @csrf
                                                        <label class="flex-1">
                                                            <span class="sr-only">Short message to the landlord (optional)</span>
                                                            <input type="text" name="message" maxlength="500" placeholder="Short message to the landlord (optional)" class="field !h-10 text-sm">
                                                        </label>
                                                        <button type="submit" class="btn btn-primary !h-10 text-sm sm:px-5">Apply</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>

            {{-- ================= Sidebar ================= --}}
            <aside class="lg:col-span-1">
                <div class="space-y-4 lg:sticky lg:top-24">
                    <div class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                        @if ($minPrice !== null)
                            <p class="text-xs text-muted">Rooms from</p>
                            <p class="mt-0.5">
                                <span class="font-display text-2xl font-bold text-bay">₱{{ $fmt($minPrice) }}</span>
                                <span class="text-sm text-muted">/ month</span>
                            </p>
                            @if ($maxPrice != $minPrice)
                                <p class="mt-0.5 text-xs text-muted">Up to ₱{{ $fmt($maxPrice) }} for larger rooms</p>
                            @endif
                            <p class="mt-3 text-[13px] text-muted">
                                <span class="font-semibold text-bay">{{ $house->rooms->count() }}</span>
                                {{ \Illuminate\Support\Str::plural('room', $house->rooms->count()) }} available
                            </p>
                            <a href="#rooms" class="btn btn-primary !h-10 mt-4 w-full text-sm">See available rooms</a>
                        @else
                            <p class="text-sm font-semibold text-bay">No rooms listed yet</p>
                            <p class="mt-1 text-xs text-muted">Save this place and check back soon.</p>
                        @endif

                        @if ($house->landlord?->landlordProfile)
                            <div class="mt-5 flex items-start gap-3 border-t border-line pt-5">
                                <span class="grid h-9 w-9 flex-none place-items-center rounded-lg bg-mist text-sea">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-shield"/></svg>
                                </span>
                                <div>
                                    <p class="text-xs text-muted">Listed by</p>
                                    <p class="text-sm font-semibold text-bay">{{ $house->landlord->landlordProfile->business_name }}</p>
                                    <p class="mt-0.5 text-xs text-muted">Approved by an admin</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-mist p-4 text-xs leading-relaxed text-muted">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1rem;height:1rem"><use href="#l-lock"/></svg>
                        <p>
                            <span class="font-medium text-bay">Your privacy.</span>
                            This landlord can see your trust score only after you apply, and every view is logged.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- ================= Photo lightbox ================= --}}
    @if ($count > 0)
        <dialog id="lightbox" class="m-auto w-[calc(100%-1.5rem)] max-w-5xl overflow-hidden rounded-2xl border-0 bg-transparent p-0 backdrop:bg-black/80" aria-label="Photo viewer">
            <div class="relative">
                <img id="lightboxImg" src="" alt="" class="mx-auto max-h-[80vh] w-auto max-w-full rounded-2xl bg-black object-contain">
                <button type="button" id="lbClose" class="absolute right-3 top-3 grid h-9 w-9 place-items-center rounded-full bg-white/95 text-bay hover:bg-white" aria-label="Close photo viewer">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.05rem;height:1.05rem"><use href="#l-x"/></svg>
                </button>
                @if ($count > 1)
                    <button type="button" id="lbPrev" class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-bay hover:bg-white" aria-label="Previous photo">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.05rem;height:1.05rem"><use href="#l-back"/></svg>
                    </button>
                    <button type="button" id="lbNext" class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-bay hover:bg-white" aria-label="Next photo">
                        <svg viewBox="0 0 24 24" class="ico rotate-180" style="width:1.05rem;height:1.05rem"><use href="#l-back"/></svg>
                    </button>
                @endif
                <p id="lbCount" class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-black/60 px-3 py-1 text-xs text-white"></p>
            </div>
        </dialog>

        <script>
            (function () {
                var photos = @json($gallery);
                var dlg = document.getElementById('lightbox');
                var img = document.getElementById('lightboxImg');
                var counter = document.getElementById('lbCount');
                var i = 0;

                function show(n) {
                    i = (n + photos.length) % photos.length;
                    img.src = photos[i];
                    img.alt = 'Photo ' + (i + 1) + ' of ' + photos.length;
                    counter.textContent = (i + 1) + ' / ' + photos.length;
                }

                document.querySelectorAll('[data-gallery-open]').forEach(function (b) {
                    b.addEventListener('click', function () { show(parseInt(b.dataset.galleryOpen, 10)); dlg.showModal(); });
                });
                document.getElementById('lbClose').addEventListener('click', function () { dlg.close(); });
                var prev = document.getElementById('lbPrev'), next = document.getElementById('lbNext');
                if (prev) prev.addEventListener('click', function () { show(i - 1); });
                if (next) next.addEventListener('click', function () { show(i + 1); });
                dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
                document.addEventListener('keydown', function (e) {
                    if (!dlg.open) return;
                    if (e.key === 'ArrowLeft') show(i - 1);
                    if (e.key === 'ArrowRight') show(i + 1);
                });
            })();
        </script>
    @endif
@endsection