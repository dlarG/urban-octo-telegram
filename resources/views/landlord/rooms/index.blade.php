@extends('layouts.landlord')

@section('content')
    @include('landlord._ui')

    @php
        $statusOf = fn ($r) => $r->status instanceof \BackedEnum ? $r->status->value : (string) $r->status;
        $counts   = $rooms->countBy($statusOf);
        $filters  = [
            'all'         => ['All', $rooms->count()],
            'available'   => ['Available', $counts->get('available', 0)],
            'full'        => ['Full', $counts->get('full', 0)],
            'maintenance' => ['Maintenance', $counts->get('maintenance', 0)],
            'delisted'    => ['Delisted', $counts->get('delisted', 0)],
        ];
    @endphp

    <div class="rs-page mx-auto max-w-6xl">
        <a href="{{ route('landlord.properties.show', $house) }}"
           class="inline-flex items-center gap-1 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-back"/></svg>
            {{ $house->name }}
        </a>

        <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-display text-3xl font-bold text-bay">Rooms</h1>
                <p class="mt-1 text-muted">
                    {{ $rooms->count() }} {{ \Illuminate\Support\Str::plural('room', $rooms->count()) }} in {{ $house->name }}
                </p>
            </div>
            <a href="{{ route('landlord.properties.rooms.create', $house) }}" class="btn btn-primary btn-lg">
                <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                Add room
            </a>
        </div>

        @if (session('status'))
            <div class="mt-6 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#l-check-circle"/></svg>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if ($rooms->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-16 text-center">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-mist text-sea">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.6rem;height:1.6rem"><use href="#l-bed"/></svg>
                </span>
                <h2 class="font-display mt-5 text-xl font-bold text-bay">Add your first room</h2>
                <p class="mx-auto mt-2 max-w-sm text-muted">Renters apply to a specific room, so each room you rent out needs its own entry with a price and photos.</p>
                <a href="{{ route('landlord.properties.rooms.create', $house) }}" class="btn btn-primary btn-lg mt-6">
                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                    Add a room
                </a>
            </div>
        @else
            {{-- Filter chips. Counts come from the full list, so they stay correct whichever chip is active. --}}
            <div class="mt-8 -mx-1 flex gap-2 overflow-x-auto px-1 pb-1" role="group" aria-label="Filter rooms by status">
                @foreach ($filters as $key => [$label, $count])
                    <button type="button" data-filter="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}"
                            class="filter-chip inline-flex flex-none items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-sm font-medium text-bay hover:bg-mist aria-pressed:border-[#0B3C49] aria-pressed:bg-[#0B3C49] aria-pressed:text-white">
                        {{ $label }}
                        <span class="text-xs opacity-75">{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            <div id="roomGrid" class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($rooms as $room)
                    @php
                        $cover = $room->propertyImages->firstWhere('is_primary', true) ?? $room->propertyImages->first();
                        $price = (float) $room->base_price_monthly;
                        $priceText = number_format($price, fmod($price, 1) == 0.0 ? 0 : 2);
                    @endphp
                    <article data-status="{{ $statusOf($room) }}"
                             class="group relative flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-lg">
                        <div class="relative aspect-[16/10] bg-mist">
                            @if ($cover)
                                <img src="{{ Storage::url($cover->path) }}" alt="Photo of {{ $room->room_label }}" loading="lazy"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center text-[#9BB5B3]">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:2.5rem;height:2.5rem;stroke-width:1.25"><use href="#l-image"/></svg>
                                </div>
                            @endif
                            <div class="absolute left-3 top-3 shadow-sm">
                                @include('landlord.rooms._status-badge', ['status' => $room->status])
                            </div>
                            @if ($room->propertyImages->isEmpty())
                                <span class="absolute bottom-3 left-3 rounded-md bg-white/95 px-2 py-1 text-xs font-medium text-muted">No photos yet</span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="font-display text-lg font-bold leading-snug text-bay">
                                    <a href="{{ route('landlord.properties.rooms.show', [$house, $room]) }}"
                                       class="after:absolute after:inset-0 after:content-[''] focus-visible:after:rounded-2xl">{{ $room->room_label }}</a>
                                </h2>
                                <p class="text-right">
                                    <span class="font-display text-lg font-bold text-bay">₱{{ $priceText }}</span>
                                    <span class="block text-xs text-muted">per month</span>
                                </p>
                            </div>

                            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-sm text-muted">
                                <li class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#l-{{ $room->room_type->value === 'shared' ? 'users' : 'user' }}"/></svg>
                                    {{ ucfirst($room->room_type->value) }}, fits {{ $room->capacity }}
                                </li>
                                @if ($room->has_own_bathroom)
                                    <li class="flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#l-droplet"/></svg>Own bathroom
                                    </li>
                                @endif
                                @if ($room->has_aircon)
                                    <li class="flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#l-wind"/></svg>Aircon
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="relative z-10 flex items-center justify-between border-t border-line px-4 py-3 text-sm">
                            <span class="font-medium text-sea">Manage room</span>
                            <a href="{{ route('landlord.properties.rooms.edit', [$house, $room]) }}"
                               class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 font-medium text-muted hover:bg-mist hover:text-bay"
                               aria-label="Edit {{ $room->room_label }}">
                                <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-pencil"/></svg>Edit
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <p id="noMatch" class="mt-8 hidden rounded-xl border border-line bg-white px-6 py-10 text-center text-muted">
                No rooms with this status.
            </p>

            <script>
                (function () {
                    var chips = document.querySelectorAll('[data-filter]');
                    var cards = document.querySelectorAll('#roomGrid [data-status]');
                    var none = document.getElementById('noMatch');
                    chips.forEach(function (chip) {
                        chip.addEventListener('click', function () {
                            var f = chip.dataset.filter, shown = 0;
                            chips.forEach(function (c) { c.setAttribute('aria-pressed', String(c === chip)); });
                            cards.forEach(function (card) {
                                var show = f === 'all' || card.dataset.status === f;
                                card.classList.toggle('hidden', !show);
                                if (show) shown++;
                            });
                            none.classList.toggle('hidden', shown > 0);
                        });
                    });
                })();
            </script>
        @endif
    </div>
@endsection