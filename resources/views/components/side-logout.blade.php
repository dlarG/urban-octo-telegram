@props(['label' => 'Log out'])

<form method="POST" action="{{ route('logout') }}" class="contents">
    @csrf
    <button type="submit"
            class="flex w-full items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                   text-left text-red-600 hover:bg-red-50">
        {{ $label }}
    </button>
</form>