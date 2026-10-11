@extends('layouts.renter')

@section('title', 'Profile')

@section('content')
    @php
        use App\Enums\RenterType;
        $profile    = $user->renterProfile;
        $hasId      = (bool) $profile->valid_id_path;
        $idIsFresh  = $hasId && $profile->valid_id_submitted_at;
        $initials   = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)
                        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
        $label      = 'block text-sm font-medium text-bay';
        $hint       = 'mt-1 text-xs text-muted';
    @endphp

    <div class="mx-auto max-w-7xl">

        {{-- ================= Hero ================= --}}
        <section class="relative z-20 rounded-3xl bg-bay text-white"
                x-data="{ menu: false, view: false }"
                @keydown.escape.window="menu = false; view = false">

            {{-- Background layer: the only part that clips --}}
            <div class="absolute inset-0 overflow-hidden rounded-3xl" aria-hidden="true">
                <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1600&q=70"
                    alt="" class="h-full w-full object-cover" onerror="this.style.visibility='hidden'">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0B3C49] via-[#0B3C49]/85 to-[#0B3C49]/40"></div>
            </div>

            <div class="relative flex flex-col gap-6 px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-10 sm:py-10">
                <div class="flex items-center gap-4">

                    {{-- Avatar + dropdown --}}
                    <div class="relative flex-none" @click.outside="menu = false">
                        <button type="button" @click="menu = !menu"
                                class="group relative block h-20 w-20 overflow-hidden rounded-full bg-white/15 ring-2 ring-white/40 transition hover:ring-white"
                                aria-haspopup="menu" :aria-expanded="menu.toString()" aria-label="Profile photo options">
                            @if ($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" alt="" class="h-full w-full object-cover">
                            @else
                                <span class="grid h-full w-full place-items-center text-2xl font-semibold">{{ $initials }}</span>
                            @endif
                            <span class="absolute inset-0 grid place-items-center bg-[#0B3C49]/60 opacity-0 transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100">
                                <svg viewBox="0 0 24 24" class="ico"><use href="#l-pencil"/></svg>
                            </span>
                        </button>

                        <div x-show="menu" x-cloak x-transition.origin.top.left.duration.150ms
                            class="absolute left-0 top-full z-30 mt-2 w-48 overflow-hidden rounded-xl border border-line bg-white p-1.5 text-bay shadow-xl"
                            role="menu">
                            @if ($user->profile_photo_path)
                                <button type="button" role="menuitem" @click="view = true; menu = false" class="menu-item">
                                    <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-eye"/></svg>
                                    View photo
                                </button>
                            @endif
                            <button type="button" role="menuitem" @click="menu = false; $refs.photo.click()" class="menu-item">
                                <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-upload"/></svg>
                                {{ $user->profile_photo_path ? 'Change photo' : 'Upload photo' }}
                            </button>
                        </div>

                        {{-- Belongs to the profile form below; choosing a file saves it right away --}}
                        <form method="POST" action="{{ route('renter.profile.photo') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_photo" accept="image/*" class="sr-only" tabindex="-1"
                                x-ref="photo" @change="if ($refs.photo.files.length) $refs.photo.form.submit()">
                        </form>
                    </div>

                    <div class="min-w-0">
                        <h1 class="font-display text-2xl font-bold sm:text-3xl">Your profile</h1>
                        <p class="mt-1 text-base text-white/85">Complete your profile to be able to apply for rooms.</p>
                    </div>
                </div>

                @if ($idIsFresh)
                    <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-white/15 px-3 py-1.5 text-xs font-medium sm:self-auto">
                        <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-check-circle"/></svg>
                        Valid ID on file
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-[#FBF0D6] px-3 py-1.5 text-xs font-semibold text-[#7A5410] sm:self-auto">
                        <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem"><use href="#l-alert"/></svg>
                        Valid ID needed
                    </span>
                @endif
            </div>

            {{-- Full view of the photo --}}
            @if ($user->profile_photo_path)
                <div x-show="view" x-cloak x-transition.opacity
                    class="fixed inset-0 z-50 grid place-items-center bg-[#0B3C49]/80 p-4 backdrop-blur-sm"
                    role="dialog" aria-modal="true" aria-label="Profile photo" @click="view = false">
                    <div class="relative" @click.stop>
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Your profile photo"
                            class="max-h-[85vh] max-w-full rounded-2xl object-contain shadow-2xl">
                        <button type="button" @click="view = false"
                                class="absolute -right-3 -top-3 grid h-9 w-9 place-items-center rounded-full bg-white text-bay shadow-lg hover:bg-mist"
                                aria-label="Close photo">
                            <svg viewBox="0 0 24 24" class="ico"><use href="#l-x"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </section>


        @if (! $idIsFresh)
            <div class="mt-5 flex items-start gap-3 rounded-xl bg-[#FBF0D6] px-4 py-3 text-[13px] text-[#7A5410]" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg>
                <p><span class="font-semibold">Action needed:</span> Upload a valid ID to unlock applications.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg>
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="mt-8 grid items-start gap-8 lg:grid-cols-3">

            {{-- ================= Main column ================= --}}
            <div class="space-y-8 lg:col-span-2">

                <form id="profileForm" method="POST" action="{{ route('renter.profile.update') }}" enctype="multipart/form-data"
                    x-data="{ rt: '{{ old('renter_type', $profile->renter_type->value) }}' }"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Account --}}
                    <section class="rounded-2xl border border-line bg-white p-6" aria-labelledby="account-heading">
                        <h2 id="account-heading" class="font-display text-lg font-bold text-bay">Account</h2>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="{{ $label }}">Name</label>
                                <input id="name" type="text" name="name" required value="{{ old('name', $user->name) }}"
                                       class="field mt-1.5" @error('name') aria-invalid="true" @enderror>
                                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="{{ $label }}">Phone</label>
                                <input id="phone" type="text" name="phone" required value="{{ old('phone', $user->phone) }}"
                                       class="field mt-1.5" @error('phone') aria-invalid="true" @enderror>
                                @error('phone') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="{{ $label }}">Email</label>
                                <input id="email" type="email" value="{{ $user->email }}" disabled
                                       class="field mt-1.5 cursor-not-allowed bg-mist text-muted">
                                <p class="{{ $hint }}">Email cannot be changed here.</p>
                            </div>
                        </div>
                    </section>

                    {{-- Renter details --}}
                    <section class="rounded-2xl border border-line bg-white p-6" aria-labelledby="renter-heading">
                        <h2 id="renter-heading" class="font-display text-lg font-bold text-bay">Renter details</h2>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label for="renter_type" class="{{ $label }}">I am a…</label>
                                <select id="renter_type" name="renter_type" x-model="rt" required class="field mt-1.5">
                                    @foreach (RenterType::cases() as $case)
                                        <option value="{{ $case->value }}">{{ ucfirst($case->value) }}</option>
                                    @endforeach
                                </select>
                                @error('renter_type') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                            </div>

                            {{-- Student --}}
                            <div x-show="rt === 'student'" x-cloak class="grid gap-4 sm:grid-cols-3">
                                <div class="sm:col-span-3">
                                    <label for="campus_id" class="{{ $label }}">Campus</label>
                                    <select id="campus_id" name="campus_id" class="field mt-1.5">
                                        <option value="">Select a campus</option>
                                        @foreach ($campuses as $c)
                                            <option value="{{ $c->id }}" @selected(old('campus_id', $profile->campus_id) == $c->id)>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="major" class="{{ $label }}">Major</label>
                                    <input id="major" type="text" name="major" value="{{ old('major', $profile->major) }}" class="field mt-1.5">
                                </div>
                                <div>
                                    <label for="year_level" class="{{ $label }}">Year level</label>
                                    <input id="year_level" type="text" name="year_level" value="{{ old('year_level', $profile->year_level) }}" class="field mt-1.5">
                                </div>
                            </div>

                            {{-- Worker --}}
                            <div x-show="rt === 'worker'" x-cloak class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="occupation" class="{{ $label }}">Occupation</label>
                                    <input id="occupation" type="text" name="occupation" value="{{ old('occupation', $profile->occupation) }}" class="field mt-1.5">
                                </div>
                                <div>
                                    <label for="employer" class="{{ $label }}">Employer</label>
                                    <input id="employer" type="text" name="employer" value="{{ old('employer', $profile->employer) }}" class="field mt-1.5">
                                </div>
                            </div>

                            {{-- Tourist --}}
                            <div x-show="rt === 'tourist'" x-cloak>
                                <label for="stay_duration" class="{{ $label }}">Stay duration</label>
                                <input id="stay_duration" type="text" name="stay_duration" value="{{ old('stay_duration', $profile->stay_duration) }}"
                                       placeholder="e.g. 2 weeks, 1 month" class="field mt-1.5">
                            </div>

                            {{-- Budget --}}
                            <div class="grid gap-4 border-t border-line pt-4 sm:grid-cols-2">
                                @foreach (['budget_min' => 'Min budget', 'budget_max' => 'Max budget'] as $name => $text)
                                    <div>
                                        <label for="{{ $name }}" class="{{ $label }}">{{ $text }} <span class="font-normal text-muted">per month</span></label>
                                        <div class="relative mt-1.5">
                                            {{-- <span class="pointer-events-none absolute inset-y-0 left-0 grid w-9 place-items-center text-sm text-muted" aria-hidden="true">₱</span> --}}
                                            <input id="{{ $name }}" type="number" inputmode="numeric" min="0" name="{{ $name }}"
                                                   value="{{ old($name, $profile->$name) }}" class="field pl-8">
                                        </div>
                                        @error($name) <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    {{-- Identity verification --}}
                    <section class="rounded-2xl border border-line bg-white p-6" aria-labelledby="id-heading">
                        <h2 id="id-heading" class="font-display text-lg font-bold text-bay">Identity verification</h2>

                        @if ($hasId)
                            <div class="mt-5 flex items-center gap-3 rounded-xl border border-[#CDE8E5] bg-[#E6F4F2] px-4 py-3 text-sm">
                                <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-lock"/></svg>
                                <span class="font-medium text-bay">Valid ID uploaded and locked</span>
                                <a href="{{ Storage::url($profile->valid_id_path) }}" target="_blank" rel="noopener"
                                   class="ml-auto font-medium text-sea hover:underline">View</a>
                            </div>
                            <p class="{{ $hint }}">For security, documents can't be changed after submission. Contact support if you need to update them.</p>
                        @else
                            <div class="relative mt-5">
                                <input id="valid_id" type="file" name="valid_id" required accept=".jpg,.jpeg,.png,.pdf" class="peer sr-only" data-file-input>
                                <label for="valid_id"
                                       class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-[#9BB5B3] bg-white p-4 text-sm transition-colors hover:bg-mist peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                    <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-mist text-sea">
                                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-upload"/></svg>
                                    </span>
                                    <span class="min-w-0">
                                        <span data-file-label data-default="Upload a government-issued ID" class="block truncate font-medium text-bay">Upload a government-issued ID</span>
                                        <span class="block text-xs text-muted">JPG, PNG or PDF. A clear photo or scan.</span>
                                    </span>
                                </label>
                            </div>
                            <p class="{{ $hint }}">You'll only be able to upload this once.</p>
                            @error('valid_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        @endif
                    </section>

                    {{-- Consent --}}
                    <section class="rounded-2xl border border-line bg-white p-6">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="hidden" name="trust_score_consent" value="0">
                            <input type="checkbox" name="trust_score_consent" value="1"
                                   @checked(old('trust_score_consent', $profile->trust_score_consent))
                                   class="mt-1 h-4 w-4 rounded border-[#9BB5B3] accent-[#0B3C49]">
                            <span class="text-sm leading-relaxed text-muted">
                                <span class="block font-semibold text-bay">Trust score consent</span>
                                I consent to RentStreet maintaining a trust score based on my tenancy history.
                                My score is private and only visible to landlords after I apply.
                            </span>
                        </label>
                    </section>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary btn-lg">Save profile</button>
                    </div>
                </form>

                {{-- Password --}}
                <section class="rounded-2xl border border-line bg-white p-6" aria-labelledby="password-heading">
                    <h2 id="password-heading" class="font-display text-lg font-bold text-bay">Change password</h2>

                    <form method="POST" action="{{ route('renter.profile.password') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="{{ $label }}">Current password</label>
                            <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="field mt-1.5">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="password" class="{{ $label }}">New password</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password" class="field mt-1.5">
                            </div>
                            <div>
                                <label for="password_confirmation" class="{{ $label }}">Confirm password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field mt-1.5">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-secondary btn-lg">Update password</button>
                        </div>
                    </form>
                </section>
            </div>

            {{-- ================= Side column ================= --}}
            <aside class="lg:sticky lg:top-24">
                <section class="rounded-2xl border border-line bg-white p-6" aria-labelledby="ready-heading">
                    <h2 id="ready-heading" class="font-display text-lg font-bold text-bay">Ready to apply?</h2>

                    <ul class="mt-5 space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid h-6 w-6 flex-none place-items-center rounded-full {{ $idIsFresh ? 'bg-sea text-white' : 'bg-mist text-muted' }}">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#l-{{ $idIsFresh ? 'check' : 'file' }}"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-bay">Valid ID</p>
                                <p class="text-xs text-muted">{{ $idIsFresh ? 'On file. You can apply for rooms.' : 'Upload one to unlock applications.' }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid h-6 w-6 flex-none place-items-center rounded-full {{ $profile->trust_score_consent ? 'bg-sea text-white' : 'bg-mist text-muted' }}">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#l-{{ $profile->trust_score_consent ? 'check' : 'shield' }}"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-bay">Trust score consent</p>
                                <p class="text-xs text-muted">{{ $profile->trust_score_consent ? 'Given.' : 'Not given yet.' }}</p>
                            </div>
                        </li>
                    </ul>

                    <p class="mt-5 flex items-start gap-2 rounded-lg bg-mist p-3 text-xs leading-relaxed text-muted">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:.9rem;height:.9rem"><use href="#l-lock"/></svg>
                        Your ID is used for verification only, and your trust score stays private until you apply.
                    </p>
                </section>
            </aside>
        </div>
    </div>

    <script>
        // Show the chosen file name on the styled upload buttons
        document.querySelectorAll('[data-file-input]').forEach(function (input) {
            input.addEventListener('change', function () {
                var label = input.parentElement.querySelector('[data-file-label]');
                label.textContent = input.files.length ? input.files[0].name : label.dataset.default;
            });
        });
    </script>
@endsection