@extends('layouts.renter')

@section('content')
    <h1 class="text-2xl font-semibold">My applications</h1>
    <p class="text-gray-600 mt-1">Track the rooms you've applied to.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    @if ($applications->isEmpty())
        <div class="mt-6 bg-white border rounded-lg p-8 text-center text-gray-500">
            You haven't applied to any rooms yet.
            <a href="{{ route('properties.index') }}" class="text-indigo-600 hover:underline">
                Browse properties
            </a>
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($applications as $app)
                @php
                    $house = $app->room->boardingHouse;
                    $cover = $app->room->primaryImage() ?? $house->primaryImage();
                    $statusClass = match($app->status) {
                        \App\Enums\ApplicationStatus::Submitted => 'bg-yellow-100 text-yellow-800',
                        \App\Enums\ApplicationStatus::Viewed    => 'bg-blue-100 text-blue-800',
                        \App\Enums\ApplicationStatus::Accepted  => 'bg-green-100 text-green-800',
                        \App\Enums\ApplicationStatus::Rejected  => 'bg-red-100 text-red-800',
                        \App\Enums\ApplicationStatus::Withdrawn => 'bg-gray-100 text-gray-700',
                    };
                @endphp

                <div class="bg-white border rounded-lg p-4 flex flex-col sm:flex-row gap-4">
                    @if ($cover)
                        <img src="{{ Storage::url($cover->path) }}"
                             class="w-full sm:w-32 h-32 rounded object-cover bg-gray-100" alt="">
                    @else
                        <div class="w-full sm:w-32 h-32 rounded bg-gray-100"></div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('properties.show', $house) }}"
                                   class="font-medium hover:text-indigo-600">{{ $house->name }}</a>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $app->room->room_label }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                                {{ $app->status->value }}
                            </span>
                        </div>

                        @if ($app->message)
                            <p class="text-sm text-gray-600 mt-2 italic">"{{ $app->message }}"</p>
                        @endif

                        <div class="mt-3 flex items-center gap-3 text-xs text-gray-500">
                            <span>Applied {{ $app->created_at->diffForHumans() }}</span>
                            @if ($app->responded_at)
                                <span>· Responded {{ $app->responded_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        @if (in_array($app->status->value, ['submitted', 'viewed']))
                            <form method="POST" action="{{ route('renter.applications.withdraw', $app) }}"
                                  class="mt-3"
                                  onsubmit="return confirm('Withdraw this application?');">
                                @csrf @method('DELETE')
                                <button class="text-sm text-red-600 hover:underline">Withdraw</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $applications->links() }}</div>
    @endif
@endsection