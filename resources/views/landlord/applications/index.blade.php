@extends('layouts.landlord')

@section('content')
    <h1 class="text-2xl font-semibold">Applications</h1>
    <p class="text-gray-600 mt-1">Review renters who applied to your rooms.</p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Filter chips — counts computed server-side, no client reduce --}}
    <div class="mt-6 flex flex-wrap gap-2">
        @php
            $chips = [
                'pending'  => ['Pending',  $counts['pending']],
                'accepted' => ['Accepted', $counts['accepted']],
                'rejected' => ['Rejected', $counts['rejected']],
                'all'      => ['All',      $counts['pending'] + $counts['accepted'] + $counts['rejected']],
            ];
        @endphp
        @foreach ($chips as $key => [$label, $count])
            <a href="{{ route('landlord.applications', ['status' => $key]) }}"
               class="rounded-full border px-3 py-1.5 text-sm font-medium
                      {{ $filter === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                {{ $label }} <span class="opacity-70">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    @if ($applications->isEmpty())
        <div class="mt-8 bg-white border rounded-lg p-8 text-center text-gray-500">
            No applications {{ $filter !== 'all' ? "in this category" : 'yet' }}.
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($applications as $app)
                @php
                    $statusClass = match($app->status) {
                        \App\Enums\ApplicationStatus::Submitted => 'bg-yellow-100 text-yellow-800',
                        \App\Enums\ApplicationStatus::Viewed    => 'bg-blue-100 text-blue-800',
                        \App\Enums\ApplicationStatus::Accepted  => 'bg-green-100 text-green-800',
                        \App\Enums\ApplicationStatus::Rejected  => 'bg-red-100 text-red-800',
                        \App\Enums\ApplicationStatus::Withdrawn => 'bg-gray-100 text-gray-700',
                    };
                @endphp

                <a href="{{ route('landlord.applications.show', $app) }}"
                   class="block bg-white border rounded-lg p-4 hover:shadow-sm transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium">{{ $app->renter->name }}</div>
                            <div class="text-sm text-gray-500 mt-0.5">
                                Applied to <strong>{{ $app->room->room_label }}</strong>
                                in {{ $app->room->boardingHouse->name }}
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                            {{ $app->status->value }}
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $app->created_at->diffForHumans() }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $applications->links() }}</div>
    @endif
@endsection