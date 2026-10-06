@extends('layouts.landlord')

@section('content')
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold">Edit "{{ $house->name }}"</h1>
        <p class="text-gray-600 mt-1">
            @if ($house->status === \App\Enums\PropertyStatus::Inactive)
                Saving changes will resubmit this property for review.
            @endif
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.properties.update', $house) }}"
              class="mt-6 bg-white rounded-lg border p-5">
            @csrf
            @method('PUT')
            @include('landlord.properties._form', ['house' => $house, 'amenities' => $amenities])
            <div class="mt-6 flex gap-2">
                <button type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Save changes
                </button>
                <a href="{{ route('landlord.properties.show', $house) }}"
                   class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection