@extends('layouts.renter')
@use('App\Enums\DisputeStatus')

@section('title', 'Trust score')

@section('content')
    @php
        $scoreValue = (float) $score->score;
        $pct        = max(0, min(100, $scoreValue));
        $circ       = 2 * pi() * 52;
        $ringColor  = $pct >= 75 ? '#177E89' : ($pct >= 50 ? '#C48A1A' : '#A32A2A');
        $openDisputes = $disputes->where('status', DisputeStatus::Open)->count();
    @endphp

    <div class="mx-auto max-w-5xl">
        <h1 class="font-display text-3xl font-bold text-bay">Trust score</h1>
        <p class="mt-1 max-w-2xl text-muted">
            A record of your real tenancy activity. It is private, and you always have the right to dispute a negative entry.
        </p>

        {{-- ================= Score + explainer ================= --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-5">

            <section class="rounded-2xl border border-line bg-white p-6 text-center lg:col-span-2" aria-label="Current score">
                <div class="relative mx-auto h-44 w-44">
                    <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90" role="img"
                         aria-label="Trust score {{ number_format($scoreValue, 2) }} out of 100">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#E3EBEA" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $ringColor }}" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="{{ round($circ * $pct / 100, 2) }} {{ round($circ, 2) }}"/>
                    </svg>
                    <div class="absolute inset-0 grid place-items-center">
                        <div>
                            <p class="font-display text-4xl font-bold leading-none text-bay">{{ number_format($scoreValue, 2) }}</p>
                            <p class="mt-1 text-sm text-muted">out of 100</p>
                        </div>
                    </div>
                </div>

                <p class="mt-4 flex items-center justify-center gap-1.5 text-sm text-muted">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-clock"/></svg>
                    @if ($score->last_event_at)
                        Last activity {{ $score->last_event_at->diffForHumans() }}
                    @else
                        No activity yet. You start at 100.
                    @endif
                </p>

                @if ($openDisputes > 0)
                    <a href="#disputes" class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#FBF0D6] px-3 py-1.5 text-sm font-medium text-[#7A5410]">
                        {{ $openDisputes }} open {{ \Illuminate\Support\Str::plural('dispute', $openDisputes) }}
                    </a>
                @endif
            </section>

            <section class="rounded-2xl border border-line bg-white p-6 lg:col-span-3" aria-labelledby="how-score">
                <h2 id="how-score" class="font-display text-lg font-bold text-bay">How your score works</h2>
                <ul class="mt-5 space-y-5">
                    @foreach ([
                        ['clock', 'Built from real activity', 'Paying rent on time and moving out as agreed adds to your record. Late payments and violations are logged too.'],
                        ['lock', 'Private until you apply', 'A landlord can see your score only after you apply to one of their rooms, and every view is logged.'],
                        ['chat', 'You can dispute any negative entry', 'Explain what happened and attach evidence. An admin reviews it and replies here.'],
                    ] as [$icon, $title, $text])
                        <li class="flex gap-4">
                            <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-mist text-sea">
                                <svg viewBox="0 0 24 24" class="ico"><use href="#l-{{ $icon }}"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-bay">{{ $title }}</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- ================= Event history ================= --}}
        <section class="mt-12" aria-labelledby="events-heading">
            <h2 id="events-heading" class="font-display text-xl font-bold text-bay">Event history</h2>

            @if ($events->isEmpty())
                <div class="mt-4 rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-12 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-clock"/></svg>
                    </span>
                    <p class="mt-4 font-semibold text-bay">Nothing here yet</p>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-muted">Events appear as you complete tenancies and payments.</p>
                </div>
            @else
                <ul class="mt-4 divide-y divide-[#D9E3E2] overflow-hidden rounded-2xl border border-line bg-white">
                    @foreach ($events as $event)
                        @php
                            $positive = $event->delta >= 0;
                            $dispute  = $disputes->firstWhere('trust_score_event_id', $event->id);
                        @endphp
                        <li class="p-4 sm:p-5">
                            <div class="flex items-start gap-4">
                                <span class="mt-0.5 grid h-10 w-10 flex-none place-items-center rounded-full {{ $positive ? 'bg-[#E1F2EC] text-[#0F6B5A]' : 'bg-[#FBEFEE] text-[#A32A2A]' }}">
                                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-arrow-{{ $positive ? 'up' : 'down' }}"/></svg>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="font-semibold text-bay">
                                            {{ ucfirst(str_replace('_', ' ', $event->event_type->value)) }}
                                        </p>
                                        <div class="flex-none text-right">
                                            <p class="font-display text-lg font-bold {{ $positive ? 'text-[#0F6B5A]' : 'text-[#A32A2A]' }}">
                                                {{ $positive ? '+' : '' }}{{ number_format($event->delta, 2) }}
                                            </p>
                                            <p class="text-xs text-muted">Score {{ number_format($event->score_after, 2) }}</p>
                                        </div>
                                    </div>

                                    @if ($event->reason)
                                        <p class="mt-1 text-sm leading-relaxed text-muted">{{ $event->reason }}</p>
                                    @endif

                                    <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
                                        @if ($event->tenancy?->room)
                                            <span class="inline-flex items-center gap-1">
                                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-building"/></svg>
                                                {{ $event->tenancy->room->room_label }}, {{ $event->tenancy->room->boardingHouse->name }}
                                            </span>
                                        @endif
                                        <span>{{ $event->created_at->format('M d, Y g:i A') }}</span>
                                    </p>

                                    {{-- Dispute state / form --}}
                                    @if ($dispute)
                                        <a href="#disputes" class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-mist px-3 py-1 text-xs font-medium text-bay">
                                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-chat"/></svg>
                                            Dispute {{ strtolower($dispute->status->value) }}
                                        </a>
                                    @elseif ($event->delta < 0)
                                        <details class="group mt-3 rounded-xl border border-line bg-[#FAFBFA]">
                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-xl px-4 py-2.5 text-sm font-medium text-sea hover:bg-mist [&::-webkit-details-marker]:hidden">
                                                <span class="inline-flex items-center gap-2">
                                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-chat"/></svg>
                                                    Dispute this event
                                                </span>
                                                <svg viewBox="0 0 24 24" class="ico transition-transform group-open:rotate-180" style="width:1rem;height:1rem"><use href="#l-chevron-down"/></svg>
                                            </summary>

                                            <form method="POST" action="{{ route('renter.trust.dispute', $event) }}"
                                                  enctype="multipart/form-data" class="space-y-4 border-t border-line p-4">
                                                @csrf
                                                <div>
                                                    <label for="reason-{{ $event->id }}" class="block text-sm font-medium text-bay">What is wrong with this event?</label>
                                                    <textarea id="reason-{{ $event->id }}" name="reason" required rows="3"
                                                              placeholder="Explain what happened"
                                                              class="field mt-1.5 py-3" style="height:auto"></textarea>
                                                </div>

                                                <div>
                                                    <span class="block text-sm font-medium text-bay">Evidence <span class="font-normal text-muted">(optional)</span></span>
                                                    <div class="relative mt-1.5">
                                                        <input id="evidence-{{ $event->id }}" type="file" name="evidence[]" multiple
                                                               accept=".jpg,.jpeg,.png,.pdf" class="peer sr-only" data-evidence>
                                                        <label for="evidence-{{ $event->id }}"
                                                               class="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-[#9BB5B3] bg-white p-3 text-sm transition-colors hover:bg-mist peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                                            <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-upload"/></svg>
                                                            <span class="min-w-0">
                                                                <span data-evidence-label data-default="Attach photos or PDFs" class="block truncate font-medium text-bay">Attach photos or PDFs</span>
                                                                <span class="block text-xs text-muted">JPG, PNG or PDF. You can pick several files.</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Submit dispute</button>
                                            </form>
                                        </details>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5">{{ $events->links() }}</div>
            @endif
        </section>

        {{-- ================= Disputes ================= --}}
        <section id="disputes" class="mt-12 scroll-mt-24" aria-labelledby="disputes-heading">
            <h2 id="disputes-heading" class="font-display text-xl font-bold text-bay">Your disputes</h2>

            @if ($disputes->isEmpty())
                <div class="mt-4 rounded-2xl border border-line bg-white p-6 text-sm leading-relaxed text-muted">
                    You have no disputes. If a negative event looks wrong, open it in the history above and choose
                    <span class="font-medium text-bay">Dispute this event</span>.
                </div>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($disputes as $d)
                        @php
                            $pill = match ($d->status) {
                                DisputeStatus::Open     => 'bg-[#FBF0D6] text-[#7A5410]',
                                DisputeStatus::Resolved => 'bg-[#E1F2EC] text-[#0F6B5A]',
                                default                 => 'bg-[#ECEFEF] text-[#566A70]',
                            };
                        @endphp
                        <li class="rounded-2xl border border-line bg-white p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-bay">
                                        {{ ucfirst(str_replace('_', ' ', $d->event->event_type->value)) }}
                                    </p>
                                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ $d->reason }}</p>
                                </div>
                                <span class="inline-flex flex-none items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $pill }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>{{ ucfirst($d->status->value) }}
                                </span>
                            </div>

                            @if ($d->resolution_note)
                                <div class="mt-4 rounded-lg border-l-4 border-[#177E89] bg-mist px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-sea">Admin response</p>
                                    <p class="mt-1 text-sm leading-relaxed text-bay">{{ $d->resolution_note }}</p>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <script>
        // Show how many evidence files were chosen
        document.querySelectorAll('[data-evidence]').forEach(function (input) {
            input.addEventListener('change', function () {
                var label = input.parentElement.querySelector('[data-evidence-label]');
                var n = input.files.length;
                label.textContent = n === 0 ? label.dataset.default
                    : (n === 1 ? input.files[0].name : n + ' files selected');
            });
        });
    </script>
@endsection