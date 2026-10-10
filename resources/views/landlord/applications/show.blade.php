@extends('layouts.landlord')

@section('content')
    <a href="{{ route('landlord.applications') }}" class="text-sm text-indigo-600 hover:underline">
        ← Applications
    </a>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-3 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif


    <div class="mt-4 grid gap-6 lg:grid-cols-3">

        {{-- Left: application + action --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white border rounded-lg p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold">{{ $application->renter->name }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Applied to <strong>{{ $application->room->room_label }}</strong>
                            in {{ $application->room->boardingHouse->name }}
                        </p>
                    </div>
                    <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-0.5">
                        {{ $application->status->value }}
                    </span>
                </div>

                @if ($application->message)
                    <div class="mt-4 rounded-md bg-gray-50 p-3 text-sm text-gray-700 italic">
                        "{{ $application->message }}"
                    </div>
                @endif

                <div class="mt-4 text-xs text-gray-500">
                    Applied {{ $application->created_at->format('M d, Y g:i A') }}
                </div>
            </div>

            {{-- Accept / reject actions --}}
            @if (in_array($application->status->value, ['submitted', 'viewed']))
                <div class="bg-white border rounded-lg p-5">
                    <h2 class="font-medium">Accept this application</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Creates a tenancy. Other pending applications for this room will be auto-rejected.
                    </p>

                    <form method="POST" action="{{ route('landlord.applications.accept', $application) }}"
                          class="mt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Move-in date</label>
                                <input type="date" name="start_date"
                                       value="{{ now()->addDays(3)->toDateString() }}"
                                       class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Monthly rent (₱)</label>
                                <input type="number" step="0.01" name="monthly_rent"
                                       value="{{ $application->room->base_price_monthly }}"
                                       class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message to renter (optional)</label>
                            <textarea name="response_message" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                        </div>
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Accept & create tenancy
                        </button>
                    </form>
                </div>

                <div class="bg-white border rounded-lg p-5">
                    <h2 class="font-medium text-red-700">Reject this application</h2>
                    <form method="POST" action="{{ route('landlord.applications.reject', $application) }}"
                          class="mt-3 space-y-3">
                        @csrf
                        <textarea name="reason" rows="2" required
                                  placeholder="Reason (shared with the renter)"
                                  class="block w-full rounded-md border-gray-300"></textarea>
                        <button class="rounded-md bg-white border border-red-200 text-red-700 px-4 py-2 text-sm font-medium hover:bg-red-50">
                            Reject application
                        </button>
                    </form>
                </div>
            @elseif ($application->status->value === 'accepted')
                <div class="bg-green-50 border border-green-200 rounded-lg p-5 text-sm text-green-800">
                    <div class="font-medium">Accepted</div>
                    <div class="mt-1">
                        A tenancy has been created.
                        <a href="{{ route('landlord.tenancies.index') }}" class="underline">View tenancies →</a>
                    </div>
                </div>
            @elseif ($application->status->value === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-lg p-5 text-sm text-red-800">
                    <div class="font-medium">Rejected</div>
                    @if ($application->response_message)
                        <div class="mt-1 italic">"{{ $application->response_message }}"</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right: renter card with trust score --}}
        <aside class="space-y-4">
            <div class="bg-white border rounded-lg p-5">
                <h2 class="font-medium">Renter</h2>

                <div class="mt-3 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-medium">
                        {{ strtoupper(substr($application->renter->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium">{{ $application->renter->name }}</div>
                        <div class="text-xs text-gray-500">{{ $application->renter->email }}</div>
                    </div>
                </div>

                @php $rp = $application->renter->renterProfile; @endphp
                @if ($rp)
                    <dl class="mt-4 text-sm divide-y">
                        <div class="flex justify-between py-2">
                            <dt class="text-gray-500">Type</dt>
                            <dd>{{ ucfirst($rp->renter_type->value) }}</dd>
                        </div>
                        @if ($rp->campus)
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-500">Campus</dt>
                                <dd>{{ $rp->campus->name }}</dd>
                            </div>
                        @endif
                        @if ($rp->occupation)
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-500">Occupation</dt>
                                <dd>{{ $rp->occupation }}</dd>
                            </div>
                        @endif
                        @if ($rp->budget_min || $rp->budget_max)
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-500">Budget</dt>
                                <dd>
                                    ₱{{ number_format($rp->budget_min ?? 0) }}
                                    – ₱{{ number_format($rp->budget_max ?? 0) }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                @endif
            </div>

            {{-- Trust score — governed by "no public shaming" rule --}}
            <div class="bg-white border rounded-lg p-5">
                <h2 class="font-medium flex items-center gap-2">
                    Trust score
                    <span class="text-xs font-normal text-gray-500">(logged view)</span>
                </h2>

                @if ($trustScore)
                    <div class="mt-3 text-center">
                        <div class="text-3xl font-semibold text-indigo-700">
                            {{ number_format((float) $trustScore->score, 2) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">out of 100</div>
                    </div>

                    @if ($recentEvents->isNotEmpty())
                        <div class="mt-4">
                            <div class="text-xs uppercase text-gray-500">Recent events</div>
                            <ul class="mt-1 text-sm space-y-1">
                                @foreach ($recentEvents as $e)
                                    <li class="flex items-center justify-between">
                                        <span>{{ str_replace('_', ' ', $e->event_type->value) }}</span>
                                        <span class="{{ $e->delta >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $e->delta >= 0 ? '+' : '' }}{{ number_format($e->delta, 2) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="mt-3 text-xs text-gray-500 text-center">
                            No events yet.
                        </p>
                    @endif
                @else
                    <p class="mt-3 text-sm text-gray-500">
                        Not available for this renter.
                    </p>
                @endif
            </div>
        </aside>
    </div>
@endsection