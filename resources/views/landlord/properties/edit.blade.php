{{-- resources/views/landlord/properties/edit.blade.php --}}
@extends('layouts.landlord')

@section('title', 'Edit property')

@section('content')
    <div class="mx-auto max-w-5xl">
        <h1 class="font-display text-3xl font-bold text-bay">Edit "{{ $house->name }}"</h1>

        @if ($house->status === \App\Enums\PropertyStatus::Inactive)
            <div class="mt-4 flex items-start gap-3 rounded-xl border border-[#C9DDE8] bg-[#EFF6FA] px-4 py-3.5 text-sm text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea"><use href="#l-info"/></svg>
                <p>Saving changes will resubmit this property for review.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 flex items-start gap-3 rounded-xl border border-[#E7C4C1] bg-[var(--danger-bg)] px-4 py-3.5 text-sm" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-danger"><use href="#l-alert"/></svg>
                <div class="min-w-0">
                    <p class="font-semibold text-danger">Please fix the following</p>
                    <ul class="mt-1.5 list-disc space-y-1 pl-5 text-[#6B2222]">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.properties.update', $house) }}" class="mt-6">
            @csrf
            @method('PUT')
            @include('landlord.properties._form', ['house' => $house, 'amenities' => $amenities])

            <div class="sticky bottom-4 z-20 mt-6 flex flex-col gap-2 rounded-2xl border border-line bg-white/95 p-3 shadow-lg backdrop-blur sm:flex-row-reverse sm:justify-start">
                <button type="submit" class="btn btn-primary btn-lg">Save changes</button>
                <a href="{{ route('landlord.properties.show', $house) }}" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
@endsection