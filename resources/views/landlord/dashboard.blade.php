@extends('layouts.landlord')

@section('title', 'Dashboard')

@section('content')
    @php
        use App\Enums\LandlordApprovalStatus;
        use App\Models\RentalApplication;
        use App\Models\Room;

        /*
         * Move this block into your controller once you're happy with the layout.
         * ASSUMPTIONS to check against your schema:
         *   - App\Models\Room belongs to a boardingHouse
         *   - RentalApplication has a `status` column ('pending', 'approved', 'rejected', ...)
         *   - RentalApplication has a `renter` relationship
         *   - BoardingHouse has a `name` (and optionally `address`) column, Room has a `name`
         *   - LandlordApprovalStatus has an `Approved` case
         */
        $user    = auth()->user();
        $profile = $user->landlordProfile;
        $first   = \Illuminate\Support\Str::of($user->name)->explode(' ')->first();

        $hour     = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        $isApproved  = $profile?->approval_status === LandlordApprovalStatus::Accepted;
        $isPending   = $profile?->hasSubmittedDocuments() && $profile?->approval_status === LandlordApprovalStatus::Pending;
        $isRejected  = $profile?->approval_status === LandlordApprovalStatus::Rejected;
        $hasDocs     = (bool) $profile?->hasSubmittedDocuments();

        $propertyCount = $user->boardingHouses()->count();
        $roomCount     = Room::whereHas('boardingHouse', fn ($q) => $q->where('landlord_id', $user->id))->count();

        $applications = fn () => RentalApplication::whereHas('room.boardingHouse', fn ($q) => $q->where('landlord_id', $user->id));
        $applicationCount = $applications()->count();
        $pendingCount     = $applications()->where('status', 'pending')->count();

        $recentApplications = $applications()->with(['room.boardingHouse', 'renter'])->latest()->take(5)->get();
        $recentProperties   = $user->boardingHouses()->withCount('rooms')->latest()->take(4)->get();

        $createUrl = \Illuminate\Support\Facades\Route::has('landlord.properties.create')
            ? route('landlord.properties.create')
            : route('landlord.properties.index');

        // Setup checklist
        $steps = [
            ['label' => 'Submit your valid ID and business permit', 'done' => $hasDocs,           'href' => route('landlord.profile'),            'cta' => 'Open profile'],
            ['label' => 'Get your account approved',                'done' => $isApproved,        'href' => route('landlord.profile'),            'cta' => 'Check status'],
            ['label' => 'Add your first property',                  'done' => $propertyCount > 0, 'href' => $createUrl,                           'cta' => 'Add property'],
            ['label' => 'Add rooms to your property',               'done' => $roomCount > 0,     'href' => route('landlord.properties.index'),   'cta' => 'Manage rooms'],
        ];
        $doneCount   = collect($steps)->where('done', true)->count();
        $setupDone   = $doneCount === count($steps);
        $nextStep    = collect($steps)->firstWhere('done', false);

        $statusBadge = function ($status) {
            $value = strtolower($status instanceof \BackedEnum ? $status->value : (string) $status);
            return match (true) {
                in_array($value, ['pending', 'submitted', 'under_review']) => 'bg-[#FFF3D6] text-[#7A5200]',
                in_array($value, ['approved', 'accepted', 'active'])        => 'bg-[#E3F1EF] text-[#0F5D66]',
                in_array($value, ['rejected', 'declined', 'cancelled'])     => 'bg-[#FBEFEE] text-[#A32A2A]',
                default                                                     => 'bg-mist text-muted',
            };
        };
    @endphp

    {{-- ============ Header ============ --}}
    <section class="rounded-2xl bg-bay px-6 py-7 text-white sm:px-8 sm:py-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm text-white/65">{{ now()->format('l, F j') }}</p>
                <h1 class="font-display mt-1 text-3xl font-bold sm:text-4xl">{{ $greeting }}, {{ $first }}.</h1>
                <p class="mt-2 max-w-xl text-[15px] leading-relaxed text-white/75">
                    @if ($pendingCount > 0)
                        You have {{ $pendingCount }} {{ \Illuminate\Support\Str::plural('application', $pendingCount) }} waiting for your review.
                    @elseif ($isApproved && $propertyCount === 0)
                        Your account is approved. Add your first property so renters can find it.
                    @elseif ($isApproved)
                        No applications are waiting. Your listings are live and open to renters.
                    @else
                        Finish setting up your account to start listing properties.
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($pendingCount > 0)
                    <a href="{{ route('landlord.applications') }}" class="btn btn-lantern">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-inbox"/></svg>
                        Review applications
                    </a>
                @endif
                @if ($isApproved)
                    <a href="{{ $createUrl }}" class="btn border border-white/25 text-white hover:bg-white/10">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                        Add property
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ============ Account status notice ============ --}}
    @if (! $hasDocs)
        <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#F0DDA8] bg-[#FFF8E6] px-4 py-3.5 text-sm text-[#5C4200]" role="status">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-[#B07A00]"><use href="#l-alert"/></svg>
            <div class="min-w-0 flex-1">
                <p class="font-semibold">Submit your documents to start listing</p>
                <p class="mt-0.5 text-[#7A5E1A]">We need a valid ID and your business permit before you can publish a property.</p>
            </div>
            <a href="{{ route('landlord.profile') }}" class="btn btn-secondary flex-none">Complete profile</a>
        </div>
    @elseif ($isPending)
        <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#C9DDE8] bg-[#EFF6FA] px-4 py-3.5 text-sm text-bay" role="status">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><use href="#l-clock"/></svg>
            <div>
                <p class="font-semibold">Your documents are under review</p>
                <p class="mt-0.5 text-muted">We'll notify you as soon as there's a decision. You don't need to do anything right now.</p>
            </div>
        </div>
    @elseif ($isRejected)
        <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#E7C4C1] bg-[var(--danger-bg)] px-4 py-3.5 text-sm" role="alert">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-danger"><use href="#l-alert"/></svg>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-danger">Your documents were not approved</p>
                @if ($profile->rejection_reason)
                    <p class="mt-0.5 text-[#6B2222]">Reason: {{ $profile->rejection_reason }}</p>
                @endif
            </div>
            <a href="{{ route('landlord.profile') }}" class="btn btn-danger flex-none">Resubmit documents</a>
        </div>
    @endif

    {{-- ============ Stats strip ============ --}}
    @php
        $stats = [
            ['label' => 'Properties',   'value' => $propertyCount,    'icon' => 'building', 'hint' => $propertyCount === 1 ? '1 listing' : $propertyCount . ' listings', 'href' => route('landlord.properties.index')],
            ['label' => 'Rooms',        'value' => $roomCount,        'icon' => 'bed',      'hint' => 'across all properties',                                          'href' => route('landlord.properties.index')],
            ['label' => 'Applications', 'value' => $applicationCount, 'icon' => 'inbox',    'hint' => $pendingCount > 0 ? $pendingCount . ' to review' : 'none waiting', 'href' => route('landlord.applications')],
            ['label' => 'Account',      'value' => $profile?->approval_status?->label() ?? 'Not started', 'icon' => 'shield', 'hint' => $isApproved ? 'ready to list' : 'see profile', 'href' => route('landlord.profile'), 'text' => true],
        ];
    @endphp

    <section class="mt-5 grid grid-cols-2 overflow-hidden rounded-2xl border border-line bg-white lg:grid-cols-4" aria-label="Overview">
        @foreach ($stats as $s)
            <a href="{{ $s['href'] }}"
               class="group relative flex flex-col gap-3 p-5 transition-colors hover:bg-mist/60
                      {{ ! $loop->first ? 'lg:border-l lg:border-line' : '' }}
                      {{ $loop->index % 2 === 1 ? 'border-l border-line lg:border-l' : '' }}
                      {{ $loop->index >= 2 ? 'border-t border-line lg:border-t-0' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">{{ $s['label'] }}</span>
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.05rem;height:1.05rem"><use href="#l-{{ $s['icon'] }}"/></svg>
                    </span>
                </div>
                <div>
                    <p class="font-display {{ ($s['text'] ?? false) ? 'text-xl' : 'text-3xl' }} font-bold leading-none text-bay">{{ $s['value'] }}</p>
                    <p class="mt-1.5 text-xs text-muted">{{ $s['hint'] }}</p>
                </div>
            </a>
        @endforeach
    </section>

    {{-- ============ Setup checklist ============ --}}
    @unless ($setupDone)
        <section class="mt-5 rounded-2xl border border-line bg-white p-5 sm:p-6" aria-labelledby="setup-title">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="setup-title" class="font-display text-lg font-bold text-bay">Get set up</h2>
                    <p class="mt-0.5 text-sm text-muted">{{ $doneCount }} of {{ count($steps) }} done</p>
                </div>
                <div class="h-2 w-full max-w-[14rem] overflow-hidden rounded-full bg-mist" role="progressbar"
                     aria-valuemin="0" aria-valuemax="{{ count($steps) }}" aria-valuenow="{{ $doneCount }}">
                    <div class="h-full rounded-full bg-sea" style="width: {{ ($doneCount / count($steps)) * 100 }}%"></div>
                </div>
            </div>

            <ul class="mt-4 divide-y divide-line">
                @foreach ($steps as $step)
                    @php $isNext = ! $step['done'] && $step === $nextStep; @endphp
                    <li class="flex items-center gap-3 py-3">
                        @if ($step['done'])
                            <span class="grid h-6 w-6 flex-none place-items-center rounded-full bg-sea text-white">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem;stroke-width:2.5"><use href="#l-check"/></svg>
                            </span>
                        @else
                            <span class="h-6 w-6 flex-none rounded-full border-2 {{ $isNext ? 'border-sea' : 'border-line' }}"></span>
                        @endif
                        <span class="min-w-0 flex-1 text-sm {{ $step['done'] ? 'text-muted line-through decoration-line' : 'font-medium text-ink' }}">
                            {{ $step['label'] }}
                        </span>
                        @if ($isNext)
                            <a href="{{ $step['href'] }}" class="btn btn-primary flex-none">{{ $step['cta'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endunless

    {{-- ============ Main grid ============ --}}
    <div class="mt-5 grid gap-5 lg:grid-cols-3">

        {{-- Recent applications --}}
        <section class="rounded-2xl border border-line bg-white lg:col-span-2" aria-labelledby="apps-title">
            <div class="flex items-center justify-between border-b border-line px-5 py-4 sm:px-6">
                <h2 id="apps-title" class="font-display text-lg font-bold text-bay">Recent applications</h2>
                @if ($applicationCount > 0)
                    <a href="{{ route('landlord.applications') }}" class="text-sm font-medium text-sea hover:underline">View all</a>
                @endif
            </div>

            @forelse ($recentApplications as $app)
                @php
                    $renterName = $app->renter?->name ?? 'Applicant';
                    $ini = collect(preg_split('/\s+/', trim($renterName)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
                @endphp
                <a href="{{ route('landlord.applications') }}"
                   class="flex items-center gap-3.5 border-b border-line px-5 py-3.5 transition-colors last:border-0 hover:bg-mist/50 sm:px-6">
                    <span class="grid h-10 w-10 flex-none place-items-center rounded-full bg-mist text-sm font-semibold text-bay" aria-hidden="true">{{ $ini }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-ink">{{ $renterName }}</p>
                        <p class="truncate text-sm text-muted">
                            {{ $app->room?->name ?? 'Room' }} at {{ $app->room?->boardingHouse?->name ?? 'your property' }}
                        </p>
                    </div>
                    <div class="flex flex-none flex-col items-end gap-1">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusBadge($app->status) }}">
                            {{ $app->status instanceof \BackedEnum && method_exists($app->status, 'label') ? $app->status->label() : \Illuminate\Support\Str::headline($app->status instanceof \BackedEnum ? $app->status->value : (string) $app->status) }}
                        </span>
                        <span class="text-xs text-muted">{{ $app->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                        <svg viewBox="0 0 24 24" class="ico" style="width:1.5rem;height:1.5rem"><use href="#l-inbox"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-semibold text-ink">No applications yet</p>
                    <p class="mx-auto mt-1 max-w-xs text-sm text-muted">
                        When a renter applies for one of your rooms, it will show up here.
                    </p>
                </div>
            @endforelse
        </section>

        {{-- Right column --}}
        <div class="space-y-5">

            {{-- Quick actions --}}
            <section class="rounded-2xl border border-line bg-white p-5" aria-labelledby="quick-title">
                <h2 id="quick-title" class="font-display text-lg font-bold text-bay">Quick actions</h2>
                <div class="mt-3 space-y-1">
                    @if ($isApproved)
                        <a href="{{ $createUrl }}" class="menu-item">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea"><svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-plus"/></svg></span>
                            Add a property
                        </a>
                    @endif
                    <a href="{{ route('landlord.applications') }}" class="menu-item">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea"><svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-inbox"/></svg></span>
                        Review applications
                    </a>
                    <a href="{{ route('landlord.tenancies.index') }}" class="menu-item">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea"><svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-users"/></svg></span>
                        View tenancies
                    </a>
                    <a href="{{ route('landlord.profile') }}" class="menu-item">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-mist text-sea"><svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-user-circle"/></svg></span>
                        Update profile
                    </a>
                </div>
            </section>

            {{-- Listing tip --}}
            <section class="rounded-2xl bg-mist p-5">
                <div class="flex items-start gap-3">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><use href="#l-image"/></svg>
                    <div>
                        <h2 class="text-sm font-semibold text-bay">Photos get rooms rented</h2>
                        <p class="mt-1 text-sm leading-relaxed text-muted">
                            Listings with clear photos of the room, bathroom and entrance tend to get more applications. Add at least three per room.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- ============ Your properties ============ --}}
    <section class="mt-5 rounded-2xl border border-line bg-white" aria-labelledby="props-title">
        <div class="flex items-center justify-between border-b border-line px-5 py-4 sm:px-6">
            <h2 id="props-title" class="font-display text-lg font-bold text-bay">Your properties</h2>
            @if ($propertyCount > 0)
                <a href="{{ route('landlord.properties.index') }}" class="text-sm font-medium text-sea hover:underline">Manage all</a>
            @endif
        </div>

        @if ($recentProperties->isNotEmpty())
            <div class="grid gap-px bg-line sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($recentProperties as $house)
                    <a href="{{ route('landlord.properties.index') }}" class="group flex flex-col gap-3 bg-white p-5 transition-colors hover:bg-mist/50">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-mist text-bay group-hover:bg-white">
                            <svg viewBox="0 0 24 24" class="ico"><use href="#l-building"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink">{{ $house->name }}</p>
                            @if (! empty($house->address))
                                <p class="mt-0.5 flex items-center gap-1 truncate text-xs text-muted">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#l-pin"/></svg>
                                    <span class="truncate">{{ $house->address }}</span>
                                </p>
                            @endif
                        </div>
                        <p class="text-xs text-muted">{{ $house->rooms_count }} {{ \Illuminate\Support\Str::plural('room', $house->rooms_count) }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center px-6 py-12 text-center">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-mist text-sea">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.5rem;height:1.5rem"><use href="#l-building"/></svg>
                </span>
                <p class="mt-3 text-sm font-semibold text-ink">You haven't added a property yet</p>
                <p class="mx-auto mt-1 max-w-sm text-sm text-muted">
                    @if ($isApproved)
                        Add your boarding house and its rooms to start receiving applications.
                    @else
                        Once your account is approved, you can add your boarding house here.
                    @endif
                </p>
                @if ($isApproved)
                    <a href="{{ $createUrl }}" class="btn btn-primary mt-4">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                        Add property
                    </a>
                @endif
            </div>
        @endif
    </section>
@endsection