@extends('layouts.renter')

@section('title', 'My applications')

@section('content')
    <div class="mx-auto max-w-7xl">
        @include('partials.renter-profile-banner')

        {{-- ================= Header ================= --}}
        <section class="relative isolate overflow-hidden rounded-3xl bg-bay text-white">
            <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70"
                alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/40"></div>

            <div class="flex flex-col gap-5 px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-10 sm:py-10">
                <div class="max-w-xl">
                    <h1 class="font-display text-2xl font-bold sm:text-3xl">My applications</h1>
                    <p class="mt-3 text-base leading-relaxed text-white/85">
                        Track each room you applied to, from submitted to accepted.
                    </p>
                </div>

                @if (! $applications->isEmpty())
                    <a href="{{ route('renter.search') }}" class="btn btn-lantern self-start sm:self-auto">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-search"/></svg>
                        Find more places
                    </a>
                @endif
            </div>
        </section>

        @if (session('status'))
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#CDE8E5] bg-[#E6F4F2] px-4 py-3 text-[13px] text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1rem;height:1rem"><use href="#l-check-circle"/></svg>
                <p class="font-medium">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg>
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- ================= Content ================= --}}
        <div class="mt-8">
            @if ($applications->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#9BB5B3] bg-white px-6 py-14 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.4rem;height:1.4rem"><use href="#l-file"/></svg>
                    </span>
                    <h2 class="font-display mt-4 text-lg font-bold text-bay">No applications yet</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted">
                        When you apply to a room, you can follow its progress here.
                    </p>
                    <a href="{{ route('renter.search') }}" class="btn btn-primary btn-lg mt-5 text-sm">Find a place</a>
                </div>
            @else
                <p class="text-[13px] text-muted">
                    <span class="font-semibold text-bay">{{ $applications->total() }}</span>
                    {{ \Illuminate\Support\Str::plural('application', $applications->total()) }}
                </p>

                <div class="mt-4 space-y-4">
                    @foreach ($applications as $app)
                        @php
                            $house = $app->room->boardingHouse;
                            $cover = $app->room->primaryImage() ?? $house->primaryImage();

                            $status    = $app->status;
                            $isRejected  = $status === \App\Enums\ApplicationStatus::Rejected;
                            $isWithdrawn = $status === \App\Enums\ApplicationStatus::Withdrawn;
                            $canWithdraw = in_array($status->value, ['submitted', 'viewed']);

                            // Position in the tracker: 0 submitted, 1 viewed, 2 accepted/rejected
                            $step = match ($status) {
                                \App\Enums\ApplicationStatus::Submitted => 0,
                                \App\Enums\ApplicationStatus::Viewed    => 1,
                                \App\Enums\ApplicationStatus::Accepted,
                                \App\Enums\ApplicationStatus::Rejected  => 2,
                                \App\Enums\ApplicationStatus::Withdrawn => null,
                            };
                            $steps = ['Submitted', 'Viewed', $isRejected ? 'Rejected' : 'Accepted'];

                            $badge = match ($status) {
                                \App\Enums\ApplicationStatus::Submitted => 'bg-[#FBF1DC] text-[#8A5F10]',
                                \App\Enums\ApplicationStatus::Viewed    => 'bg-[#E3EEF6] text-[#1F5F8B]',
                                \App\Enums\ApplicationStatus::Accepted  => 'bg-[#E6F4F2] text-[#0F6B5E]',
                                \App\Enums\ApplicationStatus::Rejected  => 'bg-[#F8E6E6] text-[#A32A2A]',
                                \App\Enums\ApplicationStatus::Withdrawn => 'bg-[#EEF1F1] text-muted',
                            };
                        @endphp

                        <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition-shadow hover:shadow-lg sm:flex-row">

                            {{-- Photo --}}
                            <div class="relative aspect-[16/9] bg-mist sm:aspect-auto sm:w-48 sm:flex-none">
                                @if ($cover)
                                    <img src="{{ Storage::url($cover->path) }}" alt="Photo of {{ $house->name }}" loading="lazy"
                                         class="h-full w-full object-cover {{ $isWithdrawn ? 'opacity-60 grayscale' : '' }}">
                                @else
                                    <div class="grid h-full min-h-[8rem] w-full place-items-center text-[#9BB5B3]">
                                        <svg viewBox="0 0 24 24" class="ico" style="width:2rem;height:2rem;stroke-width:1.25"><use href="#l-image"/></svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h2 class="font-display text-lg font-bold leading-snug text-bay">
                                            <a href="{{ route('renter.properties.show', $house) }}"
                                               class="after:absolute after:inset-0 after:content-[''] focus-visible:after:rounded-2xl">{{ $house->name }}</a>
                                        </h2>
                                        <p class="mt-0.5 text-sm font-medium text-bay/80">{{ $app->room->room_label }}</p>
                                        <p class="mt-1 flex items-center gap-1.5 text-xs text-muted">
                                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-pin"/></svg>
                                            <span class="truncate">{{ $house->barangay }}, {{ $house->city }}</span>
                                        </p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize {{ $badge }}">
                                        {{ $status->value }}
                                    </span>
                                </div>

                                {{-- Progress tracker --}}
                                @if ($step !== null)
                                    <ol class="mt-4 flex items-center gap-2 text-[11px] font-medium" aria-label="Application progress">
                                        @foreach ($steps as $i => $label)
                                            @php
                                                $reached = $i <= $step;
                                                $dot = ! $reached
                                                    ? 'bg-mist text-muted'
                                                    : (($isRejected && $i === 2) ? 'bg-[#A32A2A] text-white' : 'bg-sea text-white');
                                            @endphp
                                            <li class="flex items-center gap-1.5" @if ($i === $step) aria-current="step" @endif>
                                                <span class="grid h-5 w-5 place-items-center rounded-full text-[10px] font-semibold {{ $dot }}">{{ $i + 1 }}</span>
                                                <span class="{{ $reached ? 'text-bay' : 'text-muted' }}">{{ $label }}</span>
                                            </li>
                                            @if (! $loop->last)
                                                <li aria-hidden="true" class="h-px w-5 sm:w-10 {{ $i < $step ? 'bg-sea' : 'bg-line' }}"></li>
                                            @endif
                                        @endforeach
                                    </ol>
                                @else
                                    <p class="mt-4 text-xs text-muted">You withdrew this application.</p>
                                @endif

                                {{-- Message --}}
                                @if ($app->message)
                                    <blockquote class="mt-4 rounded-xl bg-mist px-3.5 py-2.5 text-sm italic text-bay/80">
                                        "{{ $app->message }}"
                                    </blockquote>
                                @endif

                                {{-- Footer --}}
                                <div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-4">
                                    <p class="text-xs text-muted">
                                        Applied {{ $app->created_at->diffForHumans() }}
                                        @if ($app->responded_at)
                                            <span aria-hidden="true">·</span> Responded {{ $app->responded_at->diffForHumans() }}
                                        @endif
                                    </p>

                                    @if ($canWithdraw)
                                        <form method="POST" action="{{ route('renter.applications.withdraw', $app) }}"
                                              class="relative z-10"
                                              onsubmit="return confirm('Withdraw this application?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-secondary text-xs text-[#A32A2A] hover:bg-[#F8E6E6]">
                                                Withdraw
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">{{ $applications->links() }}</div>
            @endif
        </div>
    </div>
@endsection