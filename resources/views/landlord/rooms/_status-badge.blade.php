@php
    use App\Enums\RoomStatus;
    $classes = match($status) {
        RoomStatus::Available   => 'bg-green-100 text-green-800',
        RoomStatus::Full        => 'bg-orange-100 text-orange-800',
        RoomStatus::Maintenance => 'bg-yellow-100 text-yellow-800',
        RoomStatus::Delisted    => 'bg-gray-100 text-gray-700',
    };
@endphp
<span class="inline-flex shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $classes }}">
    {{ $status->value }}
</span>