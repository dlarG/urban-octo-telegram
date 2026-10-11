{{-- Renders a line icon for an amenity.
     Usage: @include('renter._amenity-icon', ['key' => $amenity->icon_key, 'name' => $amenity->name])
     Matches on icon_key first, then the amenity name. Unknown amenities get a check mark. --}}
@once
<svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true">
    <symbol id="am-wifi" viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/></symbol>
    <symbol id="am-video" viewBox="0 0 24 24"><path d="m16 13 5.22 3.48a.5.5 0 0 0 .78-.42V7.87a.5.5 0 0 0-.78-.42L16 11"/><rect x="2" y="6" width="14" height="12" rx="2"/></symbol>
    <symbol id="am-wind" viewBox="0 0 24 24"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></symbol>
    <symbol id="am-fan" viewBox="0 0 24 24"><path d="M10.827 16.379a6.082 6.082 0 0 1-8.618-7.002l5.412 1.45a6.082 6.082 0 0 1 7.002-8.618l-1.45 5.412a6.082 6.082 0 0 1 8.618 7.002l-5.412-1.45a6.082 6.082 0 0 1-7.002 8.618l1.45-5.412Z"/><path d="M12 12v.01"/></symbol>
    <symbol id="am-droplet" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></symbol>
    <symbol id="am-bolt" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></symbol>
    <symbol id="am-flame" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14-.22-4.05 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.43-2.29 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></symbol>
    <symbol id="am-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="am-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="am-car" viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></symbol>
    <symbol id="am-washer" viewBox="0 0 24 24"><rect x="3" y="2" width="18" height="20" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M7 6h.01M11 6h.01"/></symbol>
    <symbol id="am-shield" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="am-book" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></symbol>
    <symbol id="am-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="am-sliders" viewBox="0 0 24 24"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></symbol>
</svg>
@endonce
@php
    $k = strtolower(trim(($key ?? '') . ' ' . ($name ?? '')));
    $iconId = match (true) {
        str_contains($k, 'wifi') || str_contains($k, 'wi-fi') || str_contains($k, 'internet') => 'wifi',
        str_contains($k, 'cctv') || str_contains($k, 'camera')                                  => 'video',
        str_contains($k, 'aircon') || str_contains($k, 'air con') || str_contains($k, 'air-con') || str_contains($k, 'air condition') => 'wind',
        str_contains($k, 'fan')                                                                 => 'fan',
        str_contains($k, 'water')                                                               => 'droplet',
        str_contains($k, 'electric') || str_contains($k, 'power') || str_contains($k, 'outlet') => 'bolt',
        str_contains($k, 'kitchen') || str_contains($k, 'cook')                                 => 'flame',
        str_contains($k, 'park')                                                                => 'car',
        str_contains($k, 'laundry') || str_contains($k, 'washer') || str_contains($k, 'washing') => 'washer',
        str_contains($k, 'guard') || str_contains($k, 'security') || str_contains($k, 'lock')   => 'shield',
        str_contains($k, 'study') || str_contains($k, 'desk') || str_contains($k, 'library')    => 'book',
        str_contains($k, 'common') || str_contains($k, 'lounge') || str_contains($k, 'social')  => 'users',
        default                                                                                 => 'check',
    };
@endphp
<svg viewBox="0 0 24 24" class="ico {{ $class ?? '' }}" style="{{ $style ?? 'width:1rem;height:1rem' }}" aria-hidden="true"><use href="#am-{{ $iconId }}"/></svg>