@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
          {{ $active ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
    @if($icon)
        <x-dynamic-component :component="$icon" class="w-5 h-5" />
    @endif
    {{ $slot }}
</a>