@extends('layouts.landlord')

@section('content')
    <a href="{{ route('landlord.tenancies.index') }}" class="text-sm text-indigo-600 hover:underline">
        ← Tenancies
    </a>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-3 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="mt-4 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $tenancy->renter->name }}</h1>
            <p class="text-gray-600 mt-1">
                {{ $tenancy->room->room_label }} · {{ $tenancy->room->boardingHouse->name }}
            </p>
        </div>

        @if ($tenancy->isActive())
            <form method="POST" action="{{ route('landlord.tenancies.end', $tenancy) }}"
                  class="flex items-center gap-2"
                  onsubmit="return confirm('End this tenancy?');">
                @csrf
                <input type="text" name="reason" placeholder="Reason (required)" required
                       class="rounded-md border-gray-300 text-sm">
                <button class="rounded-md border border-red-200 text-red-700 px-3 py-1.5 text-sm hover:bg-red-50">
                    End tenancy
                </button>
            </form>
        @endif
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3 text-sm">
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Start date</div>
            <div class="font-medium mt-1">{{ $tenancy->start_date->format('M d, Y') }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Monthly rent</div>
            <div class="font-medium mt-1">₱{{ number_format($tenancy->monthly_rent, 2) }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-500">Status</div>
            <div class="font-medium mt-1">{{ $tenancy->status->value }}</div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-medium">Payments</h2>
        <p class="text-sm text-gray-500 mt-1">
            Payments are configured in Phase 7. For now, this tenancy is live.
        </p>
    </div>
@endsection