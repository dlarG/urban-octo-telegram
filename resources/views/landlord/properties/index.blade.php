{{-- resources/views/landlord/properties/index.blade.php --}}
@extends('layouts.landlord')

@section('title', 'My properties')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-bay">My properties</h1>
            <p class="mt-1 text-muted">Manage your boarding houses.</p>
        </div>

        @can('create', \App\Models\BoardingHouse::class)
            <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-plus"/></svg>
                Add property
            </a>
        @endcan
    </div>

    {{-- The success message (session('status')) is already printed by the layout, so it is not repeated here. --}}

    @php $profile = auth()->user()->landlordProfile; @endphp

    @if ($profile && ! $profile->hasSubmittedDocuments())
        <div class="mt-6 flex items-start gap-3 rounded-xl border border-[#F0DDA8] bg-[#FFF8E6] px-4 py-3.5 text-sm text-[#5C4200]" role="status">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-[#B07A00]"><use href="#l-alert"/></svg>
            <div class="min-w-0 flex-1">
                <p><strong class="font-semibold">Action needed:</strong> Complete your profile and upload your documents.</p>
            </div>
            <a href="{{ route('landlord.profile') }}" class="btn btn-secondary flex-none">Go to profile</a>
        </div>
    @elseif ($profile && $profile->hasSubmittedDocuments() && $profile->approval_status === \App\Enums\LandlordApprovalStatus::Pending)
        <div class="mt-6 flex items-start gap-3 rounded-xl border border-[#C9DDE8] bg-[#EFF6FA] px-4 py-3.5 text-sm text-bay" role="status">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><use href="#l-clock"/></svg>
            <p>Your account is still under review. You'll be able to add properties once approved.</p>
        </div>
    @elseif ($profile && $profile->approval_status === \App\Enums\LandlordApprovalStatus::Rejected)
        <div class="mt-6 flex items-start gap-3 rounded-xl border border-[#E7C4C1] bg-[var(--danger-bg)] px-4 py-3.5 text-sm text-danger" role="alert">
            <svg viewBox="0 0 24 24" class="ico mt-0.5"><use href="#l-alert"/></svg>
            <p>Your documents were rejected. Contact support to re-upload.</p>
        </div>
    @endif

    @if ($houses->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-14 text-center">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-mist text-sea">
                <svg viewBox="0 0 24 24" class="ico" style="width:1.6rem;height:1.6rem"><use href="#l-building"/></svg>
            </span>
            <p class="font-display mt-4 text-lg font-bold text-bay">No properties yet.</p>
            @can('create', \App\Models\BoardingHouse::class)
                <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary mt-5">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-plus"/></svg>
                    Create your first listing
                </a>
            @endcan
        </div>
    @else
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($houses as $house)
                <a href="{{ route('landlord.properties.show', $house) }}"
                   class="group flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-lg">

                    <div class="relative aspect-[16/10] bg-mist">
                        @php $img = $house->primaryImage(); @endphp
                        @if ($img)
                            <img src="{{ Storage::url($img->path) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-[#8BA7A5]">
                                <svg viewBox="0 0 24 24" class="ico" style="width:2.25rem;height:2.25rem;stroke-width:1.25"><use href="#l-image"/></svg>
                                <span class="text-sm">No photo yet</span>
                            </div>
                        @endif
                        <div class="absolute left-3 top-3">
                            @include('landlord.properties._status-badge', ['status' => $house->status])
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-2 p-4">
                        <h3 class="font-display truncate text-lg font-bold text-bay">{{ $house->name }}</h3>
                        <p class="flex items-center gap-1.5 truncate text-sm text-muted">
                            <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#l-pin"/></svg>
                            <span class="truncate">{{ $house->barangay }}, {{ $house->city }}</span>
                        </p>
                        <p class="mt-auto flex items-center gap-1.5 border-t border-line pt-3 text-sm text-muted">
                            <svg viewBox="0 0 24 24" class="ico text-sea" style="width:1rem;height:1rem"><use href="#l-bed"/></svg>
                            {{ $house->rooms_count }} {{ Str::plural('room', $house->rooms_count) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $houses->links() }}</div>
    @endif
@endsection