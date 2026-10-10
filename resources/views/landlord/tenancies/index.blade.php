@extends('layouts.landlord')

@section('content')
    <h1 class="text-2xl font-semibold">Tenancies</h1>
    <p class="text-gray-600 mt-1">Active and past tenants.</p>

    @if ($tenancies->isEmpty())
        <div class="mt-6 bg-white border rounded-lg p-8 text-center text-gray-500">
            No tenancies yet. Accept an application to start one.
        </div>
    @else
        <div class="mt-6 bg-white border rounded-lg divide-y">
            @foreach ($tenancies as $t)
                <a href="{{ route('landlord.tenancies.show', $t) }}"
                   class="block p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium">{{ $t->renter->name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $t->room->room_label }} · {{ $t->room->boardingHouse->name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Started {{ $t->start_date->format('M d, Y') }}
                                @if ($t->end_date) · Ends {{ $t->end_date->format('M d, Y') }} @endif
                            </div>
                        </div>
                        <span class="text-xs rounded-full px-2 py-0.5
                            {{ $t->status->value === 'active' ? 'bg-green-100 text-green-800' :
                               ($t->status->value === 'completed' ? 'bg-gray-100 text-gray-700' :
                               'bg-red-100 text-red-800') }}">
                            {{ $t->status->value }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $tenancies->links() }}</div>
    @endif
@endsection