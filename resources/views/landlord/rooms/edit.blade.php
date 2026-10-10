@extends('layouts.landlord')

@section('content')
    @include('landlord._ui')

    <div class="rs-page mx-auto max-w-5xl">
        <a href="{{ route('landlord.properties.rooms.show', [$house, $room]) }}"
           class="inline-flex items-center gap-1 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-back"/></svg>
            {{ $room->room_label }}
        </a>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <h1 class="font-display text-3xl font-bold text-bay">Edit room</h1>
            @include('landlord.rooms._status-badge', ['status' => $room->status])
        </div>
        <p class="mt-1 text-muted">{{ $room->room_label }} in {{ $house->name }}.</p>

        @if ($errors->any())
            <div class="mt-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#l-alert"/></svg>
                <p>Please fix the highlighted fields and try again.</p>
            </div>
        @endif

        <form id="roomForm" method="POST" action="{{ route('landlord.properties.rooms.update', [$house, $room]) }}"
              class="mt-8 max-w-3xl rounded-2xl border border-line bg-white" novalidate>
            @csrf @method('PUT')
            <div class="p-5 sm:p-8">
                @include('landlord.rooms._form', ['room' => $room])
            </div>
            <div class="sticky bottom-0 flex flex-col-reverse gap-2 rounded-b-2xl border-t border-line bg-white/95 p-4 backdrop-blur sm:flex-row sm:justify-end sm:px-8">
                <a href="{{ route('landlord.properties.rooms.show', [$house, $room]) }}" class="btn btn-secondary btn-lg">Cancel</a>
                <button type="submit" id="saveBtn" class="btn btn-primary btn-lg">Save changes</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('roomForm').addEventListener('submit', function () {
            var b = document.getElementById('saveBtn');
            b.textContent = 'Saving…';
            setTimeout(function () { b.disabled = true; }, 0);
        });
    </script>
@endsection