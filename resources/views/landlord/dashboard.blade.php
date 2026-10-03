@extends('layouts.landlord')

@section('content')
    <h1 class="text-2xl font-semibold">Hi, {{ auth()->user()->name }} 👋</h1>

    @php $profile = auth()->user()->landlordProfile; @endphp

    @if (! $profile?->onboarding_completed_at)
        <div class="mt-4 rounded-md bg-yellow-50 text-yellow-800 px-4 py-3 text-sm">
            <strong>Action needed:</strong> Complete your landlord profile to start listing properties.
            <a href="{{ route('landlord.onboarding') }}" class="underline font-medium">Finish onboarding →</a>
        </div>
    @elseif ($profile->approval_status === \App\Enums\LandlordApprovalStatus::Pending)
        <div class="mt-4 rounded-md bg-blue-50 text-blue-800 px-4 py-3 text-sm">
            Your documents are under review. We'll notify you once verified.
        </div>
    @elseif ($profile->approval_status === \App\Enums\LandlordApprovalStatus::Rejected)
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            Your submission was rejected.
            <a href="{{ route('landlord.onboarding') }}" class="underline font-medium">Resubmit →</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Properties</div>
            <div class="text-2xl font-semibold">{{ auth()->user()->boardingHouses()->count() }}</div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Applications</div>
            <div class="text-2xl font-semibold">
                {{ \App\Models\RentalApplication::whereHas('room.boardingHouse', fn($q) => $q->where('landlord_id', auth()->id()))->count() }}
            </div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Account status</div>
            <div class="text-2xl font-semibold">
                {{ $profile?->approval_status->label() ?? '—' }}
            </div>
        </div>
    </div>
@endsection