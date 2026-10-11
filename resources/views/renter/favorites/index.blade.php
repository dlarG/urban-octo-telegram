@extends('layouts.renter')

@section('content')
    <h1 class="text-2xl font-semibold">Favorites</h1>
    <p class="text-gray-600 mt-1">Boarding houses you've saved to compare.</p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif

    @if ($favorites->isEmpty())
        <div class="mt-6 bg-white border rounded-lg p-8 text-center text-gray-500">
            No favorites yet.
            <a href="{{ route('renter.search') }}" class="text-indigo-600 hover:underline">
                Browse properties
            </a>
        </div>
    @else
        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($favorites as $favorite)
                @php
                    $house = $favorite->boardingHouse;
                    $cover = $house->primaryImage();
                @endphp
                <div class="relative bg-white rounded-lg border hover:shadow-md transition overflow-hidden">
                    <a href="{{ route('renter.properties.show', $house) }}" class="block">
                        <div class="aspect-[16/10] bg-gray-100">
                            @if ($cover)
                                <img src="{{ Storage::url($cover->path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                    No photo
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium truncate">{{ $house->name }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5 truncate">
                                {{ $house->barangay }}, {{ $house->city }}
                            </p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('renter.favorites.toggle', $house) }}"
                          class="absolute top-3 right-3 z-10">
                        @csrf
                        <button type="submit"
                                class="grid h-9 w-9 place-items-center rounded-full bg-red-500 text-white hover:bg-red-600 transition"
                                aria-label="Remove from favorites">
                            <svg viewBox="0 0 24 24" class="ico"
                                 style="width:1.15rem;height:1.15rem;fill:currentColor;">
                                <use href="#l-heart"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $favorites->links() }}</div>
    @endif
@endsection