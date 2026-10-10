@extends('layouts.public')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-semibold">Find a place in Sogod</h1>
        <p class="text-gray-600 mt-1">
            {{ $houses->total() }} {{ \Illuminate\Support\Str::plural('property', $houses->total()) }} available
        </p>

        {{-- Filters --}}
        <form method="GET" x-data="{ open: false }" class="mt-6">
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Search by name..."
                       class="flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                <button type="button" @click="open = !open"
                        class="rounded-md border px-4 py-2 text-sm hover:bg-gray-50">
                    Filters
                </button>

                <button type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Search
                </button>
            </div>

            <div x-show="open" x-cloak class="mt-4 bg-white border rounded-lg p-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gender policy</label>
                    <select name="gender" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">Any</option>
                        <option value="male_only" @selected(request('gender') === 'male_only')>Male only</option>
                        <option value="female_only" @selected(request('gender') === 'female_only')>Female only</option>
                        <option value="mixed" @selected(request('gender') === 'mixed')>Mixed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Min price (₱)</label>
                    <input type="number" name="min" value="{{ request('min') }}"
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Max price (₱)</label>
                    <input type="number" name="max" value="{{ request('max') }}"
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="allows_cooking" value="1" @checked(request('allows_cooking'))>
                        <span class="text-sm text-gray-700">Allows cooking</span>
                    </label>
                    <label class="ml-4 inline-flex items-center gap-2">
                        <input type="checkbox" name="near" value="1" @checked(request('near'))>
                        <span class="text-sm text-gray-700">Near Sogod center (5 km)</span>
                    </label>
                </div>
            </div>
        </form>

        {{-- Results --}}
        @if ($houses->isEmpty())
            <div class="mt-8 bg-white border rounded-lg p-8 text-center text-gray-500">
                No properties match your filters.
            </div>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($houses as $house)
                    @php $cover = $house->primaryImage(); @endphp
                    <a href="{{ route('properties.show', $house) }}"
                       class="block bg-white rounded-lg border hover:shadow-md transition overflow-hidden">
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
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $house->rooms_count }} {{ \Illuminate\Support\Str::plural('room', $house->rooms_count) }} available
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $houses->links() }}</div>
        @endif
    </div>
@endsection