@php
    use App\Enums\PropertyStatus;
    $classes = match($status) {
        PropertyStatus::Active        => 'bg-green-100 text-green-800',
        PropertyStatus::PendingReview => 'bg-yellow-100 text-yellow-800',
        PropertyStatus::Inactive      => 'bg-gray-100 text-gray-700',
        PropertyStatus::Suspended     => 'bg-red-100 text-red-800',
    };
@endphp
<span class="inline-flex shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $classes }}">
    {{ str_replace('_', ' ', $status->value) }}
</span>