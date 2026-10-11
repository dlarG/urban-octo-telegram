{{-- Usage:
     @include('partials.location-modal', [
         'modalId' => 'loc-'.$house->id,
         'lat'     => $house->lat,
         'lng'     => $house->lng,
         'title'   => $house->name,
     ])
--}}

<div x-data="{ open: false }"
     x-init="
        $watch('open', v => {
            if (v) {
                $nextTick(() => {
                    const el = document.getElementById('{{ $modalId }}-map');
                    if (el && ! el.dataset.initialized) {
                        window.rsInitMapViewer(el);
                        el.dataset.initialized = '1';
                    }
                });
            }
        })
     "
     @keydown.escape.window="open = false">

    {{-- Trigger --}}
    <button type="button" @click="open = true"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-sea hover:underline">
        <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-pin"/></svg>
        View location
    </button>

    {{-- Modal --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative flex h-full items-center justify-center p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-line px-5 py-3">
                    <div>
                        <h2 class="font-display text-lg font-bold text-bay">{{ $title }}</h2>
                        <p class="text-xs text-muted">Sogod, Southern Leyte</p>
                    </div>
                    <button type="button" @click="open = false"
                            class="grid h-9 w-9 place-items-center rounded-full text-muted hover:bg-mist"
                            aria-label="Close">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-x"/></svg>
                    </button>
                </div>
                <div id="{{ $modalId }}-map"
                     class="h-80 w-full bg-mist"
                     data-map-viewer
                     data-lat="{{ $lat }}"
                     data-lng="{{ $lng }}"></div>
                <div class="flex items-center justify-between border-t border-line px-5 py-3 text-sm">
                    <span class="text-muted">
                        {{ number_format((float) $lat, 6) }}, {{ number_format((float) $lng, 6) }}
                    </span>
                    <a href="https://www.openstreetmap.org/?mlat={{ $lat }}&mlon={{ $lng }}#map=17/{{ $lat }}/{{ $lng }}"
                       target="_blank" rel="noopener"
                       class="font-medium text-sea hover:underline">
                        Open in OpenStreetMap →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>