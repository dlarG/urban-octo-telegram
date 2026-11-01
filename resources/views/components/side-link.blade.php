@props(['href', 'icon', 'active' => false, 'badge' => null])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->class([
        'group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
        'bg-white/10 text-white' => $active,
        'text-white/70 hover:bg-white/5 hover:text-white' => ! $active,
   ]) }}>

    {{-- Active indicator --}}
    @if ($active)
        <span class="absolute -left-3 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-[var(--lantern)]" aria-hidden="true"></span>
    @endif

    <svg viewBox="0 0 24 24" class="ico {{ $active ? 'text-[var(--lantern)]' : 'text-white/60 group-hover:text-white' }}">
        <use href="#l-{{ $icon }}"/>
    </svg>

    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>

    @if ($badge)
        <span class="grid h-5 min-w-[1.25rem] flex-none place-items-center rounded-full bg-[var(--lantern)] px-1.5 text-[11px] font-bold text-[var(--ink)]">
            <span class="sr-only">{{ $badge }} pending</span>
            <span aria-hidden="true">{{ $badge > 99 ? '99+' : $badge }}</span>
        </span>
    @endif
</a>