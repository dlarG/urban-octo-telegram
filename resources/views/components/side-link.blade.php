@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->class([
       'relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
       'bg-white/10 text-white before:absolute before:bottom-2 before:left-0 before:top-2 before:w-0.5 before:rounded-full before:bg-[#F2B544]' => $active,
       'text-white/70 hover:bg-white/5 hover:text-white' => ! $active,
   ]) }}>
    @if ($icon)
        <svg viewBox="0 0 24 24" class="ico" aria-hidden="true"><use href="#l-{{ $icon }}"/></svg>
    @endif
    <span>{{ $slot }}</span>
</a>