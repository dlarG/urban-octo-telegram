{{-- Shared design tokens, button styles and icons for landlord pages.
     Included once per page via @once. Move this into layouts/landlord.blade.php when you restyle the shell. --}}
@once
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --bay: #0B3C49; --sea: #177E89; --mist: #EEF3F2; --paper: #FAFBFA;
        --ink: #12242A; --muted: #566A70; --line: #D9E3E2; --lantern: #F2B544;
        --danger: #A32A2A; --danger-bg: #FBEFEE;
    }
    .rs-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; color: var(--ink); }
    .rs-page .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
    .bg-bay { background: var(--bay); } .bg-mist { background: var(--mist); }
    .text-bay { color: var(--bay); } .text-sea { color: var(--sea); }
    .text-muted { color: var(--muted); } .text-danger { color: var(--danger); }
    .border-line { border-color: var(--line); }
    .ico { width: 1.25rem; height: 1.25rem; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex: none; }

    .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; height: 2.5rem; padding: 0 1rem;
           border-radius: .375rem; font-size: .875rem; font-weight: 500; line-height: 1; cursor: pointer; transition: background-color .15s, border-color .15s; white-space: nowrap; }
    .btn-lg { height: 3rem; font-size: 1rem; }
    .btn-primary { background: var(--bay); color: #fff; } .btn-primary:hover { background: #08303a; }
    .btn-secondary { background: #fff; color: var(--bay); border: 1px solid var(--line); } .btn-secondary:hover { background: var(--mist); }
    .btn-danger { background: #fff; color: var(--danger); border: 1px solid #E7C4C1; } .btn-danger:hover { background: var(--danger-bg); }
    .btn-danger-solid { background: var(--danger); color: #fff; } .btn-danger-solid:hover { background: #862222; }
    .btn:disabled { opacity: .65; cursor: not-allowed; }

    .field { width: 100%; height: 3rem; border-radius: .375rem; border: 1px solid var(--line); background: #fff; padding: 0 .875rem; font-size: 1rem; color: var(--ink); box-shadow: none; }
    .field:focus { border-color: var(--sea); box-shadow: 0 0 0 3px rgb(23 126 137 / .18); outline: none; }
    .field[aria-invalid="true"] { border-color: var(--danger); }

    .rs-page a:focus-visible, .rs-page button:focus-visible { outline: 2px solid var(--sea); outline-offset: 2px; }
    .choice-card { transition: background-color .15s, border-color .15s; }
    [data-invalid] .choice-card { border-color: var(--danger); }
</style>

<svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="l-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="l-minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
    <symbol id="l-back" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
    <symbol id="l-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="l-check-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="l-alert" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></symbol>
    <symbol id="l-bed" viewBox="0 0 24 24"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></symbol>
    <symbol id="l-user" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
    <symbol id="l-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="l-droplet" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></symbol>
    <symbol id="l-wind" viewBox="0 0 24 24"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></symbol>
    <symbol id="l-pencil" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
    <symbol id="l-trash" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6"/><path d="M14 11v6"/></symbol>
    <symbol id="l-image" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/></symbol>
    <symbol id="l-upload" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></symbol>
    <symbol id="l-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></symbol>
</svg>
@endonce