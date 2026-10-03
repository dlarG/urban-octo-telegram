@extends('layouts.auth', ['title' => 'Sign up'])

@section('width', 'max-w-lg')
@section('aside_title', 'A room in Sogod starts here.')
@section('aside_text', 'Create a free account to apply to rooms, or register your boarding house to start listing.')
@section('aside_note', 'Your valid ID is used for verification only and is never shown publicly.')

@section('content')
    @php
        $role = old('role', request('role') === 'landlord' ? 'landlord' : 'renter');
        $renterType = old('renter_type', 'student');
        $fieldErrors = ['role', 'name', 'email', 'phone', 'renter_type', 'password', 'password_confirmation', 'terms'];
        $otherErrors = collect($errors->getMessages())->except($fieldErrors)->flatten();
    @endphp

    <h1 class="font-display text-3xl font-bold text-bay sm:text-4xl">Create your account</h1>
    <p class="mt-2 text-base text-muted">It takes about a minute.</p>

    @if ($otherErrors->isNotEmpty())
        <div class="mt-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
            <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#i-alert"/></svg>
            <ul class="space-y-1">
                @foreach ($otherErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="registerForm" method="POST" action="{{ route('register') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        {{-- ================= Role ================= --}}
        <fieldset>
            <legend class="mb-2 text-sm font-medium text-bay">I want to</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="renter" class="peer sr-only" @checked($role === 'renter')>
                    <span class="flex h-full items-start gap-3 rounded-lg border border-line bg-white p-4 transition-colors hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        <span>
                            <span class="block font-semibold text-bay">Find a room</span>
                            <span class="mt-0.5 block text-sm text-muted">Browse and apply as a renter</span>
                        </span>
                    </span>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="role" value="landlord" class="peer sr-only" @checked($role === 'landlord')>
                    <span class="flex h-full items-start gap-3 rounded-lg border border-line bg-white p-4 transition-colors hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1"/><path d="M10 21v-4h4v4"/></svg>
                        <span>
                            <span class="block font-semibold text-bay">List a boarding house</span>
                            <span class="mt-0.5 block text-sm text-muted">Register as a landlord</span>
                        </span>
                    </span>
                </label>
            </div>
            @error('role')
                <p class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                </p>
            @enderror
        </fieldset>

        {{-- ================= Name ================= --}}
        <div>
            <label for="name" class="block text-sm font-medium text-bay">Full name</label>
            <input id="name" name="name" type="text" required autofocus autocomplete="name"
                   value="{{ old('name') }}" class="field mt-1.5"
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name')
                <p id="name-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- ================= Email + phone ================= --}}
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="email" class="block text-sm font-medium text-bay">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" inputmode="email"
                       value="{{ old('email') }}" placeholder="you@example.com" class="field mt-1.5"
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-bay">Mobile number</label>
                <input id="phone" name="phone" type="tel" required autocomplete="tel" inputmode="tel"
                       value="{{ old('phone') }}" placeholder="09xxxxxxxxx" class="field mt-1.5"
                       @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                @error('phone')
                    <p id="phone-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>

        {{-- ================= Renter-only: renter type ================= --}}
        <div id="renterFields" class="{{ $role === 'renter' ? '' : 'hidden' }}">
            <fieldset @disabled($role !== 'renter')>
                <legend class="mb-2 text-sm font-medium text-bay">I am a</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach (['student' => 'Student', 'worker' => 'Worker', 'tourist' => 'Tourist', 'other' => 'Other'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="renter_type" value="{{ $value }}" class="peer sr-only" @checked($renterType === $value)>
                            <span class="block rounded-full border border-line bg-white px-4 py-2 text-sm font-medium text-bay hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-sm text-muted">You can add campus, workplace, or stay details to your profile later.</p>
                @error('renter_type')
                    <p class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </fieldset>
        </div>

        {{-- ================= Landlord-only: what happens next ================= --}}
        <div id="landlordNote" class="{{ $role === 'landlord' ? '' : 'hidden' }} flex items-start gap-3 rounded-lg border border-line bg-mist p-4">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><use href="#i-shield"/></svg>
            <div class="text-sm leading-relaxed text-bay">
                <p class="font-semibold">Next, you will verify your account</p>
                <p class="mt-1 text-muted">Upload your valid ID and business permit. An admin reviews them, and you can list your boarding house once you are approved.</p>
            </div>
        </div>

        {{-- ================= Passwords ================= --}}
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-medium text-bay">Password</label>
                <div class="relative mt-1.5">
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="field pr-12"
                           @error('password') aria-invalid="true" aria-describedby="password-error" @else aria-describedby="password-hint" @enderror>
                    <button type="button" data-toggle-password="#password"
                            class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-md text-muted hover:text-bay"
                            aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" class="ico" data-state="show"><use href="#i-eye"/></svg>
                        <svg viewBox="0 0 24 24" class="ico hidden" data-state="hide"><use href="#i-eye-off"/></svg>
                    </button>
                </div>
                @error('password')
                    <p id="password-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @else
                    <p id="password-hint" class="mt-1.5 text-sm text-muted">Use at least 8 characters.</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-bay">Confirm password</label>
                <div class="relative mt-1.5">
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="field pr-12"
                           @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
                    <button type="button" data-toggle-password="#password_confirmation"
                            class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-md text-muted hover:text-bay"
                            aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" class="ico" data-state="show"><use href="#i-eye"/></svg>
                        <svg viewBox="0 0 24 24" class="ico hidden" data-state="hide"><use href="#i-eye-off"/></svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p id="password_confirmation-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>

        {{-- ================= Terms ================= --}}
        <div>
            <label class="flex cursor-pointer items-start gap-2.5">
                <input type="checkbox" name="terms" value="1" required @checked(old('terms'))
                       class="mt-0.5 h-5 w-5 flex-none rounded border-gray-300 text-[#177E89] accent-[#177E89] focus:ring-[#177E89]">
                <span class="text-sm leading-relaxed text-bay">
                    I agree to the
                    <a href="#" class="font-medium text-sea underline-offset-2 hover:underline">Terms of Service</a>
                    and
                    <a href="#" class="font-medium text-sea underline-offset-2 hover:underline">Privacy Policy</a>.
                </span>
            </label>
            @error('terms')
                <p class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg><span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <button type="submit"
                class="inline-flex h-12 w-full items-center justify-center rounded-md bg-bay px-4 text-base font-medium text-white transition-colors hover:bg-[#08303a]">
            Create account
        </button>
    </form>

    <p class="mt-8 border-t border-line pt-6 text-center text-sm text-muted">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-sea hover:underline">Log in</a>
    </p>

    {{-- Role toggle (vanilla JS, no Alpine required) --}}
    <script>
        (function () {
            var form = document.getElementById('registerForm');
            var renterFields = document.getElementById('renterFields');
            var landlordNote = document.getElementById('landlordNote');
            var renterSet = renterFields.querySelector('fieldset');

            function sync() {
                var role = form.querySelector('input[name="role"]:checked');
                var isLandlord = role && role.value === 'landlord';
                renterFields.classList.toggle('hidden', isLandlord);
                landlordNote.classList.toggle('hidden', !isLandlord);
                renterSet.disabled = isLandlord; // renter_type isn't submitted for landlords
            }

            form.querySelectorAll('input[name="role"]').forEach(function (r) {
                r.addEventListener('change', sync);
            });
            sync();
        })();
    </script>
@endsection