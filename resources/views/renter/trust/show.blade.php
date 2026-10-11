@extends('layouts.renter')
@use('App\Enums\DisputeStatus')

@section('content')
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold">Trust score</h1>
        <p class="text-gray-600 mt-1">
            Your score is built from real tenancy activity. Landlords can only see it after you apply to their property.
        </p>

        {{-- Current score --}}
        <div class="mt-6 bg-white border rounded-lg p-6 text-center">
            <div class="text-4xl font-semibold text-indigo-700">
                {{ number_format((float) $score->score, 2) }}
            </div>
            <div class="text-sm text-gray-500 mt-1">out of 100</div>
            @if ($score->last_event_at)
                <div class="text-xs text-gray-500 mt-2">
                    Last activity: {{ $score->last_event_at->diffForHumans() }}
                </div>
            @else
                <div class="text-xs text-gray-500 mt-2">No activity yet — you're starting at 100.</div>
            @endif
        </div>

        {{-- Events --}}
        <div class="mt-8">
            <h2 class="text-lg font-medium">Event history</h2>

            @if ($events->isEmpty())
                <div class="mt-3 bg-white border rounded-lg p-6 text-center text-gray-500">
                    No events yet. They'll appear here as you complete tenancies and payments.
                </div>
            @else
                <div class="mt-3 bg-white border rounded-lg divide-y">
                    @foreach ($events as $event)
                        @php
                            $positive = $event->delta >= 0;
                            $bg = $positive ? 'text-green-700' : 'text-red-700';
                        @endphp
                        <div class="p-4 flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium">
                                    {{ str_replace('_', ' ', ucfirst($event->event_type->value)) }}
                                </div>
                                @if ($event->reason)
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $event->reason }}</p>
                                @endif
                                @if ($event->tenancy?->room)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $event->tenancy->room->room_label }} ·
                                        {{ $event->tenancy->room->boardingHouse->name }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $event->created_at->format('M d, Y g:i A') }}
                                </p>
                                @if ($event->delta < 0 && ! $disputes->contains('trust_score_event_id', $event->id))
                                    <details class="mt-2">
                                        <summary class="text-sm text-indigo-600 cursor-pointer hover:underline">Dispute this event</summary>
                                        <form method="POST" action="{{ route('renter.trust.dispute', $event) }}"
                                            enctype="multipart/form-data" class="mt-2 space-y-2">
                                            @csrf
                                            <textarea name="reason" required rows="2"
                                                    placeholder="Explain why this event is wrong"
                                                    class="block w-full rounded-md border-gray-300 text-sm"></textarea>
                                            <input type="file" name="evidence[]" multiple accept=".jpg,.jpeg,.png,.pdf"
                                                class="block w-full text-sm">
                                            <button class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">
                                                Submit dispute
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-semibold {{ $bg }}">
                                    {{ $positive ? '+' : '' }}{{ number_format($event->delta, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    → {{ number_format($event->score_after, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $events->links() }}</div>
            @endif
        </div>

        {{-- Disputes --}}
        <div class="mt-8">
            <h2 class="text-lg font-medium">Your disputes</h2>

            @if ($disputes->isEmpty())
                <div class="mt-3 bg-white border rounded-lg p-6 text-center text-gray-500 text-sm">
                    You have no disputes. If a negative event appears that you believe is wrong, you can dispute it below.
                </div>
            @else
                <div class="mt-3 space-y-3">
                    @foreach ($disputes as $d)
                        <div class="bg-white border rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium">
                                        Dispute of {{ str_replace('_', ' ', $d->event->event_type->value) }}
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $d->reason }}</p>
                                </div>
                                <span class="text-xs rounded-full px-2 py-0.5
                                    {{ $d->status === DisputeStatus::Open ? 'bg-yellow-100 text-yellow-800' :
                                       ($d->status === DisputeStatus::Resolved ? 'bg-green-100 text-green-800' :
                                       'bg-gray-100 text-gray-700') }}">
                                    {{ $d->status->value }}
                                </span>
                            </div>
                            @if ($d->resolution_note)
                                <p class="text-sm text-gray-700 mt-2 italic">
                                    <strong>Admin:</strong> {{ $d->resolution_note }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection