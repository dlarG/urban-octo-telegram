<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Account' }} — RentStreet</title>
    <meta name="theme-color" content="#0B3C49">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --bay: #0B3C49;
            --sea: #177E89;
            --mist: #EEF3F2;
            --paper: #FAFBFA;
            --ink: #12242A;
            --muted: #566A70;
            --line: #D9E3E2;
            --lantern: #F2B544;
            --danger: #A32A2A;
            --danger-bg: #FBEFEE;
        }
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; background: var(--paper); color: var(--ink); }
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .bg-bay { background: var(--bay); }
        .bg-mist { background: var(--mist); }
        .text-bay { color: var(--bay); }
        .text-sea { color: var(--sea); }
        .text-muted { color: var(--muted); }
        .border-line { border-color: var(--line); }
        .text-danger { color: var(--danger); }
        .ico { width: 1.25rem; height: 1.25rem; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex: none; }
        .field { width: 100%; height: 3rem; border-radius: 0.375rem; border: 1px solid var(--line); background: #fff; padding: 0 0.875rem; font-size: 1rem; color: var(--ink); box-shadow: none; }
        .field:focus { border-color: var(--sea); box-shadow: 0 0 0 3px rgb(23 126 137 / 0.18); outline: none; }
        .field[aria-invalid="true"] { border-color: var(--danger); }
        a:focus-visible, button:focus-visible { outline: 2px solid var(--sea); outline-offset: 2px; }
        .img-wrap { background: var(--bay); }
    </style>
</head>
<body class="antialiased">

<svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></symbol>
    <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol>
    <symbol id="i-eye-off" viewBox="0 0 24 24"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><path d="m2 2 20 20"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-back" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></symbol>
</svg>

<div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">

    {{-- ============ Photo panel (desktop only) ============ --}}
    <aside class="img-wrap relative isolate hidden overflow-hidden text-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col lg:justify-between lg:p-12">
        <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1400&q=70"
             alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" onerror="this.style.visibility='hidden'">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-[#0B3C49]/70 via-[#0B3C49]/40 to-[#0B3C49]/95"></div>

        <a href="{{ url('/') }}" class="flex items-center gap-2.5 self-start" aria-label="RentStreet home">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-white text-bay">
                <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
            </span>
            <span class="font-display text-xl font-bold">RentStreet</span>
        </a>

        <div>
            <p class="font-display max-w-md text-4xl font-bold leading-tight xl:text-5xl">
                Pick up where you left off.
            </p>
            <p class="mt-4 max-w-md text-lg leading-relaxed text-white/85">
                Check on your applications or manage your boarding house listings in Sogod, Southern Leyte.
            </p>

            <div class="mt-8 flex max-w-md items-start gap-3 rounded-xl bg-white/10 p-4 backdrop-blur">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-[#F2B544]"><use href="#i-shield"/></svg>
                <p class="text-sm leading-relaxed text-white/90">
                    Your account is protected. After three wrong password attempts, it is locked temporarily.
                </p>
            </div>
        </div>
    </aside>

    {{-- ============ Form panel ============ --}}
    <div class="flex min-h-screen flex-col">
        <header class="flex items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 lg:hidden" aria-label="RentStreet home">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-bay text-white">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1.35rem;height:1.35rem"><use href="#i-home"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-bay">RentStreet</span>
            </a>
            <a href="{{ url('/') }}" class="ml-auto inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-bay">
                <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#i-back"/></svg>
                Back to home
            </a>
        </header>

        <main class="flex flex-1 items-center justify-center px-5 py-8 sm:px-8">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </main>

        <footer class="px-5 py-6 text-center text-sm text-muted sm:px-8">
            Your personal data is handled under the Data Privacy Act (RA 10173).
        </footer>
    </div>
</div>

<script>
    // Show/hide password: <button data-toggle-password="#fieldId">
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        var input = document.querySelector(btn.dataset.togglePassword);
        if (!input) return;
        var on = btn.querySelector('[data-state="show"]');
        var off = btn.querySelector('[data-state="hide"]');
        btn.addEventListener('click', function () {
            var reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            btn.setAttribute('aria-pressed', String(reveal));
            btn.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
            on.classList.toggle('hidden', reveal);
            off.classList.toggle('hidden', !reveal);
        });
    });
</script>
</body>
</html>