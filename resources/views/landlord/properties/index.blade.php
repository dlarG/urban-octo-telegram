@extends('layouts.landlord')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">My properties</h1>
            <p class="text-gray-600 mt-1">Manage your boarding houses.</p>
        </div>

        @can('create', \App\Models\BoardingHouse::class)
            <a href="{{ route('landlord.properties.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                + Add property
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @php $profile = auth()->user()->landlordProfile; @endphp

    @if ($profile && ! $profile->hasSubmittedDocuments())
        <div class="mt-4 rounded-md bg-yellow-50 text-yellow-800 px-4 py-3 text-sm">
            <strong>Action needed:</strong> Complete your profile and upload your documents.
            <a href="{{ route('landlord.profile') }}" class="underline font-medium">Go to profile →</a>
        </div>
    @elseif ($profile && $profile->hasSubmittedDocuments() && $profile->approval_status === \App\Enums\LandlordApprovalStatus::Pending)
        <div class="mt-4 rounded-md bg-blue-50 text-blue-800 px-4 py-3 text-sm">
            Your account is still under review. You'll be able to add properties once approved.
        </div>
    @elseif ($profile && $profile->approval_status === \App\Enums\LandlordApprovalStatus::Rejected)
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            Your documents were rejected. Contact support to re-upload.
        </div>
    @endif

    @if ($houses->isEmpty())
        <div class="mt-8 bg-white rounded-lg border p-8 text-center">
            <p class="text-gray-500">No properties yet.</p>
            @can('create', \App\Models\BoardingHouse::class)
                <a href="{{ route('landlord.properties.create') }}"
                   class="mt-3 inline-block text-indigo-600 font-medium hover:underline">
                    Create your first listing
                </a>
            @endcan
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($houses as $house)
                <a href="{{ route('landlord.properties.show', $house) }}"
                   class="block bg-white rounded-lg border hover:shadow-sm transition">
                    <div class="aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                        @php $img = $house->primaryImage(); @endphp
                        @if ($img)
                            <img src="{{ Storage::url($img->path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                No photo yet
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-medium truncate">{{ $house->name }}</h3>
                            @include('landlord.properties._status-badge', ['status' => $house->status])
                        </div>
                        <p class="text-sm text-gray-500 mt-1 truncate">{{ $house->barangay }}, {{ $house->city }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $house->rooms_count }} {{ Str::plural('room', $house->rooms_count) }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $houses->links() }}</div>
    @endif
@endsection