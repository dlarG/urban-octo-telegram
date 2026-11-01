{{-- resources/views/landlord/properties/_status-badge.blade.php --}}
@php
    use App\Enums\PropertyStatus;
    $style = match($status) {
        PropertyStatus::Active        => ['bg-[#E3F1EF] text-[#0F5D66]', 'bg-[#177E89]'],
        PropertyStatus::PendingReview => ['bg-[#FFF3D6] text-[#7A5200]', 'bg-[#F2B544]'],
        PropertyStatus::Inactive      => ['bg-[#EEF3F2] text-[#566A70]', 'bg-[#9BB5B3]'],
        PropertyStatus::Suspended     => ['bg-[#FBEFEE] text-[#A32A2A]', 'bg-[#A32A2A]'],
    };
@endphp
<span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset ring-black/5 {{ $style[0] }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $style[1] }}" aria-hidden="true"></span>
    {{ \Illuminate\Support\Str::of($status->value)->replace('_', ' ')->ucfirst() }}
</span>