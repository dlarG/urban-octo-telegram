@extends('layouts.renter')

@section('content')
    <h1 class="text-2xl font-semibold">Hi, {{ auth()->user()->name }} 👋</h1>
    <p class="text-gray-600 mt-1">Browse boarding houses in Sogod.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        {{-- Stat tiles get wired in Phase 9 --}}
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Applications</div>
            <div class="text-2xl font-semibold">—</div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Favorites</div>
            <div class="text-2xl font-semibold">—</div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Trust score</div>
            <div class="text-2xl font-semibold">
                {{ number_format(auth()->user()->trustScore?->score ?? 0, 2) }}
            </div>
        </div>
    </div>
@endsection