@extends('layouts.landlord')

@section('content')
    @include('landlord._ui')

    <div class="rs-page mx-auto max-w-6xl">

        {{-- Breadcrumb --}}
        <a href="{{ route('landlord.properties.index') }}"
           class="inline-flex items-center gap-1 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-back"/></svg>
            My properties
        </a>

        {{-- Header --}}
        <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-display text-3xl font-bold text-bay">{{ $house->name }}</h1>
                    @include('landlord.properties._status-badge', ['status' => $house->status])
                </div>
                <p class="mt-1 text-muted">
                    {{ $house->address_line }}, {{ $house->barangay }}, {{ $house->city }}
                </p>
            </div>

            <div class="flex gap-2">
                @can('update', $house)
                    <a href="{{ route('landlord.properties.edit', $house) }}" class="btn btn-secondary">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-pencil"/></svg>
                        Edit
                    </a>
                @endcan

                @can('delete', $house)
                    <form method="POST" action="{{ route('landlord.properties.destroy', $house) }}"
                          onsubmit="return confirm('Delete this property?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger">
                            <svg viewBox="0 0 24 24" class="ico"><use href="#l-trash"/></svg>
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- Status banners --}}
        @if ($house->status === \App\Enums\PropertyStatus::PendingReview)
            <div class="mt-6 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#l-info"/></svg>
                <p>Under review. An admin will approve or reject this property soon.</p>
            </div>
        @elseif ($house->status === \App\Enums\PropertyStatus::Suspended)
            <div class="mt-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#l-alert"/></svg>
                <p><strong>Suspended.</strong> {{ $house->suspension_reason }}</p>
            </div>
        @elseif ($house->status === \App\Enums\PropertyStatus::Inactive && $house->rejection_reason)
            <div class="mt-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#l-alert"/></svg>
                <p><strong>Rejected:</strong> {{ $house->rejection_reason }}</p>
            </div>
        @endif

        @if (session('status'))
            <div class="mt-6 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#l-check-circle"/></svg>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        {{-- Rooms --}}
        <div class="mt-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-bay">Rooms</h2>
                    <p class="mt-1 text-muted">
                        {{ $house->rooms->count() }} {{ \Illuminate\Support\Str::plural('room', $house->rooms->count()) }}
                    </p>
                </div>
                <a href="{{ route('landlord.properties.rooms.create', $house) }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                    Add room
                </a>
            </div>

            @if ($house->rooms->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-12 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.6rem;height:1.6rem"><use href="#l-bed"/></svg>
                    </span>
                    <h3 class="font-display mt-4 text-lg font-bold text-bay">Add your first room</h3>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-muted">
                        Renters apply to a specific room — each needs its own entry with a price and photos.
                    </p>
                    <a href="{{ route('landlord.properties.rooms.create', $house) }}" class="btn btn-primary mt-5">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                        Add a room
                    </a>
                </div>
            @else
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($house->rooms as $room)
                        @php
                            $cover = $room->primaryImage();
                            $price = (float) $room->base_price_monthly;
                            $priceText = number_format($price, fmod($price, 1) == 0.0 ? 0 : 2);
                        @endphp
                        <a href="{{ route('landlord.properties.rooms.show', [$house, $room]) }}"
                           class="group relative flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-lg">
                            <div class="relative aspect-[16/10] bg-mist">
                                @if ($cover)
                                    <img src="{{ Storage::url($cover->path) }}" alt="" loading="lazy"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full w-full place-items-center text-[#9BB5B3]">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:2.5rem;height:2.5rem;stroke-width:1.25"><use href="#l-image"/></svg>
                                    </div>
                                @endif
                                <div class="absolute left-3 top-3">
                                    @include('landlord.rooms._status-badge', ['status' => $room->status])
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-display text-lg font-bold leading-snug text-bay">{{ $room->room_label }}</h3>
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
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Property photos --}}
        <div class="mt-12">
            <h2 class="font-display text-2xl font-bold text-bay">Property photos</h2>
            <p class="mt-1 text-muted">The cover photo appears on search results and the public listing.</p>

            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($house->propertyImages as $img)
                    <div class="relative aspect-square overflow-hidden rounded-xl border border-line bg-mist">
                        <img src="{{ Storage::url($img->path) }}" alt="" class="h-full w-full object-cover">
                        @if ($img->is_primary)
                            <span class="absolute left-2 top-2 rounded bg-bay px-2 py-0.5 text-xs font-medium text-white">Cover</span>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 flex bg-black/60 text-xs text-white">
                            @unless ($img->is_primary)
                                <form method="POST" action="{{ route('landlord.images.primary', $img) }}" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button class="w-full py-1.5 hover:bg-black/80">Set cover</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('landlord.images.destroy', $img) }}"
                                  class="flex-1 {{ $img->is_primary ? '' : 'border-l border-white/20' }}"
                                  onsubmit="return confirm('Delete this photo?');">
                                @csrf @method('DELETE')
                                <button class="w-full py-1.5 hover:bg-black/80">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('landlord.properties.images.store', $house) }}"
                      enctype="multipart/form-data"
                      class="relative flex aspect-square cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#9BB5B3] text-muted hover:border-sea hover:text-sea">
                    @csrf
                    <label for="house-image-input" class="flex cursor-pointer flex-col items-center gap-2">
                        <svg viewBox="0 0 24 24" class="ico" style="width:2rem;height:2rem"><use href="#l-upload"/></svg>
                        <span class="text-xs font-medium">Add photo</span>
                    </label>
                    <input id="house-image-input" type="file" name="image" accept="image/*" required
                           class="hidden" onchange="this.form.submit()">
                </div>
            </div>
        </div>
    </div>
@endsection