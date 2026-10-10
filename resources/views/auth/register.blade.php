@extends('layouts.auth', ['title' => 'Sign up'])

@section('width', 'max-w-lg')
@section('aside_title', 'Let us get you set up.')
@section('aside_text', 'A few quick questions so RentStreet shows you the right things from the start.')
@section('aside_note', 'Your valid ID is used for verification only and is never shown publicly.')

@section('content')
    @php
        $queryRole  = in_array(request('role'), ['renter', 'landlord'], true) ? request('role') : null;
        $role       = old('role', $queryRole);
        $fromLink   = $queryRole && ! old('role');
        $renterType = old('renter_type');

        // Errors that don't belong to a field in the wizard
        $knownFields = ['role', 'renter_type', 'business_name', 'gcash_number', 'maya_number',
                        'valid_id', 'business_permit', 'name', 'email', 'phone',
                        'password', 'password_confirmation', 'terms'];
        $otherErrors = collect($errors->getMessages())->except($knownFields)->flatten();

        $types = [
            'student' => ['Student', 'Studying at a school or university nearby',
                '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>'],
            'worker'  => ['Worker', 'Working in or around Sogod',
                '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
            'tourist' => ['Tourist', 'Visiting for a short stay',
                '<circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36z"/>'],
            'other'   => ['Something else', 'Relocating, visiting family, or between homes',
                '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>'],
        ];
    @endphp

    <style>
        .choice-card { transition: background-color .15s, border-color .15s; }
        [data-invalid] .choice-card { border-color: var(--danger); }
        [data-invalid] [data-dz] { border-color: var(--danger); }
        [data-dz][data-has-file] { border-style: solid; border-color: var(--sea); background: var(--mist); }
    </style>

    <noscript>
        <p class="rounded-lg bg-mist p-4 text-sm text-bay">Registration needs JavaScript to be turned on. Please enable it and reload this page.</p>
    </noscript>

    {{-- Progress --}}
    <div class="mb-8">
        <p id="progressLabel" class="text-sm font-medium text-muted" aria-live="polite"></p>
        <div id="progressBar" class="mt-2 flex gap-1.5" aria-hidden="true"></div>
    </div>

    @if ($otherErrors->isNotEmpty())
        <div class="mb-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
            <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#i-alert"/></svg>
            <ul class="space-y-1">
                @foreach ($otherErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="wizard" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <button type="button" id="backBtn" class="mb-6 hidden items-center gap-1.5 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#i-back"/></svg>
            Back
        </button>

        {{-- ============ STEP: role ============ --}}
        <section data-step="role" class="hidden">
            <h1 tabindex="-1" class="font-display text-3xl font-bold text-bay outline-none sm:text-4xl">What brings you to RentStreet?</h1>
            <p class="mt-2 text-base text-muted">Pick the one that fits. This sets up the right account for you.</p>

            <div data-field="role" class="mt-8">
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="relative block cursor-pointer">
                        <input type="radio" name="role" value="renter" class="peer sr-only" @checked($role === 'renter')>
                        <span class="choice-card flex h-full flex-col rounded-xl border border-line bg-white p-5 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                            <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sea ring-1 ring-[#D9E3E2]">
                                <svg viewBox="0 0 24 24" class="ico"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                            </span>
                            <span class="mt-4 block font-display text-lg font-bold text-bay">I am looking for a room</span>
                            <span class="mt-1 block text-sm leading-relaxed text-muted">Browse boarding houses, apply to rooms, and follow your applications.</span>
                        </span>
                        <span class="pointer-events-none absolute right-4 top-4 grid h-6 w-6 place-items-center rounded-full border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem;stroke-width:2.5"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                    </label>

                    <label class="relative block cursor-pointer">
                        <input type="radio" name="role" value="landlord" class="peer sr-only" @checked($role === 'landlord')>
                        <span class="choice-card flex h-full flex-col rounded-xl border border-line bg-white p-5 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                            <span class="grid h-11 w-11 place-items-center rounded-lg bg-white text-sea ring-1 ring-[#D9E3E2]">
                                <svg viewBox="0 0 24 24" class="ico"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1"/><path d="M10 21v-4h4v4"/></svg>
                            </span>
                            <span class="mt-4 block font-display text-lg font-bold text-bay">I own a boarding house</span>
                            <span class="mt-1 block text-sm leading-relaxed text-muted">List your rooms and reach renters in Sogod. Every landlord is verified first.</span>
                        </span>
                        <span class="pointer-events-none absolute right-4 top-4 grid h-6 w-6 place-items-center rounded-full border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                            <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem;stroke-width:2.5"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                    </label>
                </div>
            </div>
        </section>

        {{-- ============ STEP: renter type (renter only) ============ --}}
        <section data-step="type" class="hidden">
            <h1 tabindex="-1" class="font-display text-3xl font-bold text-bay outline-none sm:text-4xl">What kind of renter are you?</h1>
            <p class="mt-2 text-base text-muted">This helps landlords understand who is applying. You can add more details to your profile later.</p>

            <div data-field="renter_type" class="mt-8">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($types as $value => [$label, $hint, $iconPaths])
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="renter_type" value="{{ $value }}" class="peer sr-only" @checked($renterType === $value)>
                            <span class="choice-card flex h-full flex-col rounded-xl border border-line bg-white p-5 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                <span class="grid h-10 w-10 place-items-center rounded-lg bg-white text-sea ring-1 ring-[#D9E3E2]">
                                    <svg viewBox="0 0 24 24" class="ico">{!! $iconPaths !!}</svg>
                                </span>
                                <span class="mt-3 block font-display text-lg font-bold text-bay">{{ $label }}</span>
                                <span class="mt-0.5 block text-sm leading-relaxed text-muted">{{ $hint }}</span>
                            </span>
                            <span class="pointer-events-none absolute right-4 top-4 grid h-6 w-6 place-items-center rounded-full border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.9rem;height:.9rem;stroke-width:2.5"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ STEP: business (landlord only) ============ --}}
        <section data-step="business" class="hidden">
            <h1 tabindex="-1" class="font-display text-3xl font-bold text-bay outline-none sm:text-4xl">Tell us about your business</h1>
            <p class="mt-2 text-base text-muted">We use this to set up your landlord profile.</p>

            <div class="mt-8 space-y-5">
                <div data-field="business_name">
                    <label for="business_name" class="block text-sm font-medium text-bay">Business or boarding house name</label>
                    <input id="business_name" name="business_name" type="text" autocomplete="organization"
                           value="{{ old('business_name') }}" class="field mt-1.5">
                </div>

                <div>
                    <p class="text-sm font-medium text-bay">Where should rent be sent?</p>
                    <p class="mt-0.5 text-sm text-muted">Add at least one mobile wallet number.</p>
                    <div class="mt-3 grid gap-5 sm:grid-cols-2">
                        <div data-field="gcash_number">
                            <label for="gcash_number" class="block text-sm font-medium text-bay">GCash number</label>
                            <input id="gcash_number" name="gcash_number" type="tel" inputmode="tel" placeholder="09xxxxxxxxx"
                                   value="{{ old('gcash_number') }}" class="field mt-1.5">
                        </div>
                        <div data-field="maya_number">
                            <label for="maya_number" class="block text-sm font-medium text-bay">Maya number</label>
                            <input id="maya_number" name="maya_number" type="tel" inputmode="tel" placeholder="09xxxxxxxxx"
                                   value="{{ old('maya_number') }}" class="field mt-1.5">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ STEP: documents (landlord only) ============ --}}
        <section data-step="documents" class="hidden">
            <h1 tabindex="-1" class="font-display text-3xl font-bold text-bay outline-none sm:text-4xl">Verify your identity</h1>
            <p class="mt-2 text-base text-muted">An admin checks these before your listings go live. They stay private and are never shown to renters.</p>

            @if ($errors->any() && $role === 'landlord')
                <div class="mt-6 flex items-start gap-3 rounded-lg border border-line bg-mist p-4 text-sm text-bay">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#i-alert"/></svg>
                    <p>For your security, uploaded files are not kept after an error. Please choose them again.</p>
                </div>
            @endif

            <div class="mt-8 space-y-5">
                @foreach ([
                    'valid_id'        => ['Valid ID', 'Government-issued ID with your photo'],
                    'business_permit' => ['Business permit', 'Barangay or municipal business permit'],
                ] as $name => [$label, $hint])
                    <div data-field="{{ $name }}">
                        <label for="{{ $name }}" class="block text-sm font-medium text-bay">{{ $label }}</label>
                        <div class="relative mt-1.5">
                            <input id="{{ $name }}" name="{{ $name }}" type="file" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" class="peer sr-only">
                            <label for="{{ $name }}" data-dz
                                   class="flex cursor-pointer items-center gap-4 rounded-xl border border-dashed border-[#9BB5B3] bg-white p-4 transition-colors hover:bg-mist peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                <span class="grid h-11 w-11 flex-none place-items-center rounded-lg bg-mist text-sea">
                                    <svg viewBox="0 0 24 24" class="ico"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span data-file-name data-default="Choose a file" class="block truncate font-medium text-bay">Choose a file</span>
                                    <span class="block text-sm text-muted">{{ $hint }}. JPG, PNG or PDF, up to 5 MB.</span>
                                </span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ STEP: account (everyone, always last) ============ --}}
        <section data-step="account" class="hidden">
            <h1 tabindex="-1" class="font-display text-3xl font-bold text-bay outline-none sm:text-4xl">Create your login</h1>
            <p class="mt-2 text-base text-muted">Last step. You will use these to log in.</p>

            <div class="mt-8 space-y-5">
                <div data-field="name">
                    <label for="name" class="block text-sm font-medium text-bay">Full name</label>
                    <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" class="field mt-1.5">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div data-field="email">
                        <label for="email" class="block text-sm font-medium text-bay">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" inputmode="email"
                               value="{{ old('email') }}" placeholder="you@example.com" class="field mt-1.5">
                    </div>
                    <div data-field="phone">
                        <label for="phone" class="block text-sm font-medium text-bay">Mobile number</label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" inputmode="tel"
                               value="{{ old('phone') }}" placeholder="09xxxxxxxxx" class="field mt-1.5">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div data-field="password">
                        <label for="password" class="block text-sm font-medium text-bay">Password</label>
                        <div class="relative mt-1.5">
                            <input id="password" name="password" type="password" autocomplete="new-password" placeholder="At least 8 characters" class="field pr-12">
                            <button type="button" data-toggle-password="#password"
                                    class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-md text-muted hover:text-bay"
                                    aria-label="Show password" aria-pressed="false">
                                <svg viewBox="0 0 24 24" class="ico" data-state="show"><use href="#i-eye"/></svg>
                                <svg viewBox="0 0 24 24" class="ico hidden" data-state="hide"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                    </div>
                    <div data-field="password_confirmation">
                        <label for="password_confirmation" class="block text-sm font-medium text-bay">Confirm password</label>
                        <div class="relative mt-1.5">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="field pr-12">
                            <button type="button" data-toggle-password="#password_confirmation"
                                    class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-md text-muted hover:text-bay"
                                    aria-label="Show password" aria-pressed="false">
                                <svg viewBox="0 0 24 24" class="ico" data-state="show"><use href="#i-eye"/></svg>
                                <svg viewBox="0 0 24 24" class="ico hidden" data-state="hide"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div data-field="terms">
                    <label class="flex cursor-pointer items-start gap-2.5">
                        <input type="checkbox" name="terms" value="1" @checked(old('terms'))
                               class="mt-0.5 h-5 w-5 flex-none rounded border-gray-300 text-[#177E89] accent-[#177E89] focus:ring-[#177E89]">
                        <span class="text-sm leading-relaxed text-bay">
                            I agree to the
                            <a href="#" class="font-medium text-sea underline-offset-2 hover:underline">Terms of Service</a>
                            and
                            <a href="#" class="font-medium text-sea underline-offset-2 hover:underline">Privacy Policy</a>.
                        </span>
                    </label>
                </div>
            </div>
        </section>

        <button id="nextBtn" type="submit"
                class="mt-8 inline-flex h-12 w-full items-center justify-center rounded-md bg-bay px-4 text-base font-medium text-white transition-colors hover:bg-[#08303a] disabled:opacity-70">
            Continue
        </button>
    </form>

    <p class="mt-8 border-t border-line pt-6 text-center text-sm text-muted">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-sea hover:underline">Log in</a>
    </p>

    <script>
    (function () {
        var form = document.getElementById('wizard');
        var nextBtn = document.getElementById('nextBtn');
        var backBtn = document.getElementById('backBtn');
        var progressLabel = document.getElementById('progressLabel');
        var progressBar = document.getElementById('progressBar');

        var FLOWS = {
            renter:   ['role', 'type', 'account'],
            landlord: ['role', 'business', 'documents', 'account']
        };
        var STEP_OF = {
            role: 'role', renter_type: 'type',
            business_name: 'business', gcash_number: 'business', maya_number: 'business',
            valid_id: 'documents', business_permit: 'documents'
        };
        var MAX_BYTES = 5 * 1024 * 1024;
        var PH_NUMBER = /^(09|\+639)\d{9}$/;

        var steps = {};
        form.querySelectorAll('[data-step]').forEach(function (s) { steps[s.dataset.step] = s; });

        var serverErrors = @json($errors->messages());
        var fromLink = @json((bool) $fromLink);
        var idx = 0;

        /* ---------- helpers ---------- */
        function checkedValue(name) {
            var el = form.querySelector('input[name="' + name + '"]:checked');
            return el ? el.value : null;
        }
        function val(name) {
            var el = form.querySelector('[name="' + name + '"]');
            return el ? el.value.trim() : '';
        }
        function flow() { return FLOWS[checkedValue('role')] || FLOWS.renter; }

        function showError(name, msg) {
            var wrap = form.querySelector('[data-field="' + name + '"]');
            if (!wrap) return;
            var p = wrap.querySelector('[data-error]');
            if (!p) {
                p = document.createElement('p');
                p.setAttribute('data-error', '');
                p.id = name + '-error';
                p.className = 'mt-1.5 flex items-start gap-1.5 text-sm text-danger';
                p.innerHTML = '<svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span></span>';
                wrap.appendChild(p);
            }
            p.querySelector('span').textContent = msg;
            wrap.setAttribute('data-invalid', '');
            wrap.querySelectorAll('input, select').forEach(function (i) {
                i.setAttribute('aria-invalid', 'true');
                i.setAttribute('aria-describedby', p.id);
            });
        }
        function clearError(name) {
            var wrap = form.querySelector('[data-field="' + name + '"]');
            if (!wrap) return;
            var p = wrap.querySelector('[data-error]');
            if (p) p.remove();
            wrap.removeAttribute('data-invalid');
            wrap.querySelectorAll('input, select').forEach(function (i) {
                i.removeAttribute('aria-invalid');
                i.removeAttribute('aria-describedby');
            });
        }
        function showAll(errs) {
            var keys = Object.keys(errs);
            keys.forEach(function (k) { showError(k, errs[k]); });
            if (keys.length) {
                var wrap = form.querySelector('[data-field="' + keys[0] + '"]');
                var input = wrap && wrap.querySelector('input, select');
                if (input) input.focus();
            }
            return keys.length > 0;
        }
        function fmtSize(b) {
            return b < 1048576 ? Math.max(1, Math.round(b / 1024)) + ' KB' : (b / 1048576).toFixed(1) + ' MB';
        }

        /* ---------- per-step validation (client side; server still validates) ---------- */
        var validators = {
            role: function () {
                return checkedValue('role') ? {} : { role: 'Choose one to continue.' };
            },
            type: function () {
                return checkedValue('renter_type') ? {} : { renter_type: 'Choose the one that fits you best.' };
            },
            business: function () {
                var e = {};
                if (!val('business_name')) e.business_name = 'Enter your business or boarding house name.';
                var g = val('gcash_number').replace(/[\s-]+/g, '');
                var m = val('maya_number').replace(/[\s-]+/g, '');
                if (!g && !m) e.gcash_number = 'Add a GCash or Maya number.';
                if (g && !PH_NUMBER.test(g)) e.gcash_number = 'Use a mobile number like 09xxxxxxxxx.';
                if (m && !PH_NUMBER.test(m)) e.maya_number = 'Use a mobile number like 09xxxxxxxxx.';
                return e;
            },
            documents: function () {
                var e = {};
                [['valid_id', 'valid ID'], ['business_permit', 'business permit']].forEach(function (pair) {
                    var input = form.querySelector('[name="' + pair[0] + '"]');
                    var f = input.files[0];
                    if (!f) { e[pair[0]] = 'Upload your ' + pair[1] + '.'; return; }
                    if (!/\.(jpe?g|png|pdf)$/i.test(f.name)) { e[pair[0]] = 'Use a JPG, PNG or PDF file.'; return; }
                    if (f.size > MAX_BYTES) { e[pair[0]] = 'This file is over 5 MB. Choose a smaller one.'; }
                });
                return e;
            },
            account: function () {
                var e = {};
                if (!val('name')) e.name = 'Enter your full name.';
                var email = form.querySelector('[name="email"]');
                if (!email.value.trim()) e.email = 'Enter your email address.';
                else if (!email.checkValidity()) e.email = 'Enter a valid email address.';
                if (!val('phone')) e.phone = 'Enter your mobile number.';
                var pw = form.querySelector('[name="password"]').value;
                if (pw.length < 8) e.password = 'Use at least 8 characters.';
                if (form.querySelector('[name="password_confirmation"]').value !== pw) {
                    e.password_confirmation = 'Passwords do not match.';
                }
                if (!form.querySelector('[name="terms"]').checked) e.terms = 'You need to agree to continue.';
                return e;
            }
        };

        /* ---------- rendering ---------- */
        function render() {
            var f = flow();
            idx = Math.max(0, Math.min(idx, f.length - 1));
            var current = f[idx];

            Object.keys(steps).forEach(function (key) {
                var inFlow = f.indexOf(key) !== -1;
                steps[key].classList.toggle('hidden', key !== current);
                // Fields from the other flow are not submitted
                steps[key].querySelectorAll('input, select').forEach(function (el) { el.disabled = !inFlow; });
            });

            progressBar.innerHTML = '';
            f.forEach(function (_, i) {
                var seg = document.createElement('span');
                seg.className = 'h-1.5 flex-1 rounded-full ' + (i <= idx ? 'bg-bay' : 'bg-[#D9E3E2]');
                progressBar.appendChild(seg);
            });
            progressLabel.textContent = checkedValue('role')
                ? 'Step ' + (idx + 1) + ' of ' + f.length
                : 'Getting started';

            backBtn.classList.toggle('hidden', idx === 0);
            backBtn.classList.toggle('inline-flex', idx > 0);

            var last = idx === f.length - 1;
            nextBtn.textContent = last
                ? (checkedValue('role') === 'landlord' ? 'Create account and submit for review' : 'Create account')
                : 'Continue';

            var heading = steps[current].querySelector('h1');
            if (heading) heading.focus({ preventScroll: true });
            window.scrollTo({ top: 0 });
        }

        function go(i, push) {
            idx = i;
            render();
            if (push) history.pushState({ s: idx }, '');
            else history.replaceState({ s: idx }, '');
        }

        function next() {
            var f = flow();
            if (showAll(validators[f[idx]]())) return;
            if (idx < f.length - 1) go(idx + 1, true);
        }

        /* ---------- events ---------- */
        form.addEventListener('submit', function (e) {
            var f = flow();
            if (idx < f.length - 1) { e.preventDefault(); next(); return; }
            if (showAll(validators.account())) { e.preventDefault(); return; }
            nextBtn.textContent = 'Creating account…';
            setTimeout(function () { nextBtn.disabled = true; }, 0);
        });

        backBtn.addEventListener('click', function () { if (idx > 0) go(idx - 1, false); });

        ['input', 'change'].forEach(function (evt) {
            form.addEventListener(evt, function (e) {
                if (e.target.name) clearError(e.target.name);
            });
        });

        // Role / renter type: mouse or touch selection moves on automatically.
        // Keyboard users (detail === 0) press Continue instead.
        form.addEventListener('click', function (e) {
            var t = e.target;
            if (t.matches && t.matches('input[name="role"], input[name="renter_type"]') && e.detail > 0) {
                setTimeout(next, 180);
            }
        });
        form.querySelectorAll('input[name="role"]').forEach(function (r) {
            r.addEventListener('change', function () { render(); });
        });

        // File chooser labels
        form.querySelectorAll('input[type="file"]').forEach(function (input) {
            input.addEventListener('change', function () {
                var wrap = input.closest('[data-field]');
                var dz = wrap.querySelector('[data-dz]');
                var label = wrap.querySelector('[data-file-name]');
                var file = input.files[0];
                if (file) {
                    label.textContent = file.name + ' (' + fmtSize(file.size) + ')';
                    dz.setAttribute('data-has-file', '');
                } else {
                    label.textContent = label.dataset.default;
                    dz.removeAttribute('data-has-file');
                }
            });
        });

        window.addEventListener('popstate', function (e) {
            idx = (e.state && typeof e.state.s === 'number') ? e.state.s : 0;
            render();
        });
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { nextBtn.disabled = false; render(); }
        });

        /* ---------- initial state ---------- */
        var errorKeys = Object.keys(serverErrors || {});
        var role = checkedValue('role');
        var start = 0;

        if (errorKeys.length) {
            var f0 = flow();
            var earliest = f0.length - 1;
            errorKeys.forEach(function (k) {
                var pos = f0.indexOf(STEP_OF[k] || 'account');
                if (pos !== -1 && pos < earliest) earliest = pos;
            });
            start = earliest;
            // Uploaded files cannot be restored by the browser, so send landlords back to re-pick them
            if (role === 'landlord') start = Math.min(start, f0.indexOf('documents'));
            errorKeys.forEach(function (k) { showError(k, serverErrors[k][0]); });
        } else if (fromLink && role) {
            start = 1;
        }

        idx = start;
        render();
        history.replaceState({ s: idx }, '');
    })();
    </script>
@endsection