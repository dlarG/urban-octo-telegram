@extends('layouts.auth', ['title' => 'Log in'])

@section('content')
    <h1 class="font-display text-3xl font-bold text-bay sm:text-4xl">Welcome back</h1>
    <p class="mt-2 text-base text-muted">Log in to find a room or manage your listings.</p>

    {{-- Status message (e.g. after password reset) --}}
    @if (session('status'))
        <div class="mt-6 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay" role="status">
            <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#i-check"/></svg>
            <p>{{ session('status') }}</p>
        </div>
    @endif

    {{-- Errors that don't belong to a specific field --}}
    @php($otherErrors = collect($errors->getMessages())->except(['email', 'password'])->flatten())
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

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-bay">Email</label>
            <input id="email" name="email" type="email" required autofocus
                   autocomplete="username" inputmode="email"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   class="field mt-1.5"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')
                <p id="email-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-bay">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-sea hover:underline">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative mt-1.5">
                <input id="password" name="password" type="password" required
                       autocomplete="current-password"
                       class="field pr-12"
                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                <button type="button" data-toggle-password="#password"
                        class="absolute inset-y-0 right-0 grid w-12 place-items-center rounded-r-md text-muted hover:text-bay"
                        aria-label="Show password" aria-pressed="false">
                    <svg viewBox="0 0 24 24" class="ico" data-state="show"><use href="#i-eye"/></svg>
                    <svg viewBox="0 0 24 24" class="ico hidden" data-state="hide"><use href="#i-eye-off"/></svg>
                </button>
            </div>
            @error('password')
                <p id="password-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                    <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#i-alert"/></svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="inline-flex cursor-pointer items-center gap-2.5">
            <input type="checkbox" name="remember" @checked(old('remember'))
                   class="h-5 w-5 rounded border-gray-300 text-[#177E89] accent-[#177E89] focus:ring-[#177E89]">
            <span class="text-sm text-bay">Keep me logged in on this device</span>
        </label>

        <button type="submit"
                class="inline-flex h-12 w-full items-center justify-center rounded-md bg-bay px-4 text-base font-medium text-white transition-colors hover:bg-[#08303a]">
            Log in
        </button>
    </form>

    <div class="mt-8 space-y-3 border-t border-line pt-6 text-center text-sm text-muted">
        <p>
            New to RentStreet?
            <a href="{{ route('register') }}" class="font-medium text-sea hover:underline">Create an account</a>
        </p>
        <p>
            Own a boarding house?
            <a href="{{ route('register', ['role' => 'landlord']) }}" class="font-medium text-sea hover:underline">Register as a landlord</a>
        </p>
    </div>
@endsection