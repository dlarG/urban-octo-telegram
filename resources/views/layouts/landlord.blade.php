@extends('layouts.dashboard')

@php
    use App\Enums\LandlordApprovalStatus;
    use App\Models\RentalApplication;

    /*
     * Move to a View composer (View::composer('layouts.landlord', ...)) if you want
     * these queries out of the template. Assumes RentalApplication has a `status` column.
     */
    $lUser     = auth()->user();
    $lProfile  = $lUser->landlordProfile;
    $lApproved = $lProfile?->approval_status === LandlordApprovalStatus::Accepted;

    $lPending = RentalApplication::whereHas('room.boardingHouse', fn ($q) => $q->where('landlord_id', $lUser->id))
        ->where('status', 'pending')
        ->count();

    $lCreateUrl = \Illuminate\Support\Facades\Route::has('landlord.properties.create')
        ? route('landlord.properties.create')
        : route('landlord.properties.index');
@endphp

@section('sidebar_label', 'Landlord')

{{-- ================= Sidebar links ================= --}}
@section('sidebar')
    <x-side-link :href="route('landlord.dashboard')" :active="request()->routeIs('landlord.dashboard')" icon="grid">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('landlord.properties.index')" :active="request()->routeIs('landlord.properties.*')" icon="building">
        My properties
    </x-side-link>
    <x-side-link :href="route('landlord.applications')" :active="request()->routeIs('landlord.applications')" icon="inbox" :badge="$lPending">
        Applications
    </x-side-link>
    <x-side-link :href="route('landlord.tenancies.index')" :active="request()->routeIs('landlord.tenancies.*')" icon="handshake">
        Tenancies
    </x-side-link>

    <div class="my-3 border-t border-white/10" role="presentation"></div>

    <x-side-link :href="route('landlord.profile')" :active="request()->routeIs('landlord.profile')" icon="user-circle">
        Profile
    </x-side-link>
@endsection

{{-- ================= Sidebar footer: account status ================= --}}
@section('sidebar_footer')
    @if ($lApproved)
        <div class="flex items-center gap-3 rounded-lg bg-white/5 px-3 py-2.5">
            <span class="grid h-8 w-8 flex-none place-items-center rounded-full bg-white/10 text-[var(--lantern)]">
                <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-shield"/></svg>
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-white">Verified landlord</p>
                <p class="truncate text-xs text-white/60">You can publish listings</p>
            </div>
        </div>
    @else
        @php
            $lState = match (true) {
                ! $lProfile?->hasSubmittedDocuments()                              => ['Documents needed', 'Submit your ID and permit', 'Complete profile'],
                $lProfile?->approval_status === LandlordApprovalStatus::Rejected   => ['Not approved', 'Review the reason and resubmit', 'Resubmit'],
                default                                                            => ['Under review', 'We\'ll notify you of the result', 'View status'],
            };
        @endphp
        <a href="{{ route('landlord.profile') }}"
           class="block rounded-lg border border-[var(--lantern)]/40 bg-[var(--lantern)]/10 px-3 py-3 transition-colors hover:bg-[var(--lantern)]/15">
            <p class="flex items-center gap-2 text-sm font-medium text-white">
                <svg viewBox="0 0 24 24" class="ico text-[var(--lantern)]" style="width:1rem;height:1rem"><use href="#l-clock"/></svg>
                {{ $lState[0] }}
            </p>
            <p class="mt-1 text-xs leading-snug text-white/70">{{ $lState[1] }}</p>
            <p class="mt-2 text-xs font-medium text-[var(--lantern)]">{{ $lState[2] }}</p>
        </a>
    @endif
@endsection


{{-- ================= Account menu extras ================= --}}
@section('user_menu')
    <a href="{{ route('landlord.profile') }}" role="menuitem" class="menu-item">
        <svg viewBox="0 0 24 24" class="ico text-muted"><use href="#l-user-circle"/></svg>
        Profile and documents
    </a>
    <a href="{{ route('landlord.dashboard') }}" role="menuitem" class="menu-item">
        <svg viewBox="0 0 24 24" class="ico text-muted"><use href="#l-bell"/></svg>
        Notifications
    </a>
    <div class="my-1 border-t border-line" role="separator"></div>
@endsection