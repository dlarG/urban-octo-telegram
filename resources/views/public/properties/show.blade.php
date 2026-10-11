@extends('layouts.public')

@section('content')
    @php
        $isGuest = ! auth()->check();
    @endphp

    <div class="max-w-5xl mx-auto px-4 py-8">

        <a href="{{ route('properties.index') }}" class="text-sm text-indigo-600 hover:underline">
            ← All properties
        </a>

        
        @if ($isGuest)
            {{-- Sign-in overlay --}}
            <div class="absolute inset-0 z-10 flex items-center justify-center p-4">
                <div class="max-w-sm rounded-2xl border border-line bg-white/95 p-6 text-center shadow-lg backdrop-blur-sm">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#l-lock"/></svg>
                    </span>
                    <h2 class="font-display mt-4 text-lg font-bold text-bay">Sign in to see the full listing</h2>
                    <p class="mt-1 text-sm text-muted">
                        Photos, prices, amenities, and room details are visible to registered users.
                    </p>
                    <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary">Create account</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Everything below — blurred + overlay for guests --}}
        <div class="mt-6 relative">

            @if ($isGuest)
                {{-- Sign-in overlay --}}
                <div class="absolute inset-0 z-10 flex items-center justify-center p-4">
                    <div class="max-w-sm rounded-2xl border border-line bg-white/95 p-6 text-center shadow-lg backdrop-blur-sm">
                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                            <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#l-lock"/></svg>
                        </span>
                        <h2 class="font-display mt-4 text-lg font-bold text-bay">Sign in to see the full listing</h2>
                        <p class="mt-1 text-sm text-muted">
                            Prices, amenities, house rules, and room details are visible to registered users.
                        </p>
                        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-center">
                            <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                            <a href="{{ route('register') }}" class="btn btn-secondary">Create account</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="{{ $isGuest ? 'blur-md select-none pointer-events-none' : '' }}">

                {{-- Address --}}
                <p class="text-gray-600">
                    {{ $house->address_line }}, {{ $house->barangay }}, {{ $house->city }}, {{ $house->province }}
                </p>

                {{-- Description --}}
                @if ($house->description)
                    <div class="mt-6 bg-white border rounded-lg p-5">
                        <h2 class="font-medium">About this place</h2>
                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $house->description }}</p>
                    </div>
                @endif

                {{-- Amenities + policies --}}
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="bg-white border rounded-lg p-5">
                        <h2 class="font-medium">Amenities</h2>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse ($house->amenities as $a)
                                <span class="text-xs bg-gray-100 text-gray-700 rounded-full px-2 py-1">{{ $a->name }}</span>
                            @empty
                                <span class="text-sm text-gray-500">None listed</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="bg-white border rounded-lg p-5 text-sm space-y-1">
                        <div><span class="text-gray-500">Gender policy:</span> {{ ucfirst(str_replace('_', ' ', $house->gender_policy)) }}</div>
                        <div><span class="text-gray-500">Allows cooking:</span> {{ $house->allows_cooking ? 'Yes' : 'No' }}</div>
                        <div><span class="text-gray-500">Curfew:</span> {{ $house->curfew_time ?: 'None' }}</div>
                        <div><span class="text-gray-500">Sub-metered:</span> {{ $house->is_sub_metered ? 'Yes' : 'No' }}</div>
                        @if ($house->landlord?->landlordProfile)
                            <div><span class="text-gray-500">Listed by:</span> {{ $house->landlord->landlordProfile->business_name }}</div>
                        @endif
                    </div>
                </div>

                {{-- Rooms --}}
                <div class="mt-8">
                    <h2 class="text-lg font-medium">Available rooms</h2>

                    @if ($house->rooms->isEmpty())
                        <div class="mt-3 bg-white border rounded-lg p-6 text-center text-gray-500">
                            No rooms currently available.
                        </div>
                    @else
                        <div class="mt-3 space-y-3">
                            @foreach ($house->rooms as $room)
                                @php $cover = $room->primaryImage(); @endphp
                                <div class="bg-white border rounded-lg p-4 flex gap-4">
                                    @if ($cover)
                                        <img src="{{ Storage::url($cover->path) }}"
                                             class="w-24 h-24 rounded object-cover bg-gray-100" alt="">
                                    @else
                                        <div class="w-24 h-24 rounded bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                                            No photo
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h3 class="font-medium">{{ $room->room_label }}</h3>
                                                <p class="text-sm text-gray-500 mt-0.5">
                                                    {{ ucfirst($room->room_type->value) }} · Cap {{ $room->capacity }}
                                                    @if ($room->has_own_bathroom) · Own bath @endif
                                                    @if ($room->has_aircon) · Aircon @endif
                                                </p>
                                            </div>
                                            <p class="text-right font-semibold">
                                                &#8369;{{ number_format($room->base_price_monthly, 2) }}
                                                <span class="block text-xs text-gray-500 font-normal">per month</span>
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            @auth
                                                @if (auth()->user()->isRenter())
                                                    @php
                                                        $renterHasActiveTenancy = auth()->user()->hasActiveTenancy();
                                                        $alreadyApplied         = $room->hasBlockingApplicationFrom(auth()->id());
                                                    @endphp

                                                    @if ($renterHasActiveTenancy)
                                                        <span class="text-xs bg-blue-50 text-blue-700 rounded-full px-2 py-1">
                                                            You already have an active tenancy
                                                        </span>
                                                    @elseif ($alreadyApplied)
                                                        <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-1">
                                                            Application pending
                                                        </span>
                                                    @else
                                                        <form method="POST" action="{{ route('renter.applications.store', $room) }}"
                                                              class="flex flex-col sm:flex-row gap-2">
                                                            @csrf
                                                            <input type="text" name="message" placeholder="Short message (optional)"
                                                                   class="flex-1 text-sm rounded-md border-gray-300">
                                                            <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 whitespace-nowrap">
                                                                Apply
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-500">Only renters can apply.</span>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection