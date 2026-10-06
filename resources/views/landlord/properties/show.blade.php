@extends('layouts.landlord')

@section('content')
    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-semibold">{{ $house->name }}</h1>
                @include('landlord.properties._status-badge', ['status' => $house->status])
            </div>
            <p class="text-gray-600 mt-1">
                {{ $house->address_line }}, {{ $house->barangay }}, {{ $house->city }}
            </p>
        </div>

        <div class="flex gap-2">
            @can('update', $house)
                <a href="{{ route('landlord.properties.edit', $house) }}"
                   class="rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50">Edit</a>
            @endcan
            @can('delete', $house)
                <form method="POST" action="{{ route('landlord.properties.destroy', $house) }}"
                      onsubmit="return confirm('Delete this property?');">
                    @csrf @method('DELETE')
                    <button class="rounded-md border border-red-200 text-red-700 px-3 py-1.5 text-sm hover:bg-red-50">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if ($house->status === \App\Enums\PropertyStatus::PendingReview)
        <div class="mt-4 rounded-md bg-yellow-50 text-yellow-800 px-4 py-3 text-sm">
            Under review. An admin will approve or reject this property soon.
        </div>
    @elseif ($house->status === \App\Enums\PropertyStatus::Suspended)
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            Suspended. {{ $house->suspension_reason }}
        </div>
    @elseif ($house->status === \App\Enums\PropertyStatus::Inactive && $house->rejection_reason)
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            Rejected: {{ $house->rejection_reason }}
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-lg border p-5">
            <h2 class="font-medium">Description</h2>
            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $house->description ?: '—' }}</p>

            <h2 class="font-medium mt-6">Amenities</h2>
            <div class="mt-2 flex flex-wrap gap-2">
                @forelse ($house->amenities as $a)
                    <span class="text-xs bg-gray-100 text-gray-700 rounded-full px-2 py-1">{{ $a->name }}</span>
                @empty
                    <span class="text-sm text-gray-500">None listed</span>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 space-y-3 text-sm">
            <div><span class="text-gray-500">Gender policy:</span> {{ ucfirst(str_replace('_', ' ', $house->gender_policy)) }}</div>
            <div><span class="text-gray-500">Allows cooking:</span> {{ $house->allows_cooking ? 'Yes' : 'No' }}</div>
            <div><span class="text-gray-500">Sub-metered:</span> {{ $house->is_sub_metered ? 'Yes' : 'No' }}</div>
            <div><span class="text-gray-500">Curfew:</span> {{ $house->curfew_time ?: 'None' }}</div>
            <div><span class="text-gray-500">Water rating:</span> {{ $house->water_supply_rating ?: '—' }}/5</div>
            <div><span class="text-gray-500">Coordinates:</span> {{ $house->lat }}, {{ $house->lng }}</div>
        </div>
    </div>

    {{-- Rooms section — filled in 5B --}}
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium">Rooms</h2>
            <span class="text-sm text-gray-500">Coming in 5B</span>
        </div>
    </div>
@endsection