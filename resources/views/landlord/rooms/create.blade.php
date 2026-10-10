@extends('layouts.landlord')

@section('content')
    @include('landlord._ui')

    <div class="rs-page mx-auto max-w-5xl">
        <a href="{{ route('landlord.properties.show', $house) }}"
           class="inline-flex items-center gap-1 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-back"/></svg>
            {{ $house->name }}
        </a>
        <h1 class="font-display mt-3 text-3xl font-bold text-bay">Add a room</h1>
        <p class="mt-1 text-muted">A new room in {{ $house->name }}.</p>

        @if ($errors->any())
            <div class="mt-6 flex items-start gap-3 rounded-lg px-4 py-3 text-sm text-danger" style="background: var(--danger-bg)" role="alert">
                <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1.1rem;height:1.1rem"><use href="#l-alert"/></svg>
                <p>Please fix the highlighted fields and try again.</p>
            </div>
        @endif

        <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <form id="roomForm" method="POST" action="{{ route('landlord.properties.rooms.store', $house) }}"
                  class="rounded-2xl border border-line bg-white" novalidate>
                @csrf
                <div class="p-5 sm:p-8">
                    @include('landlord.rooms._form')
                </div>
                <div class="sticky bottom-0 flex flex-col-reverse gap-2 rounded-b-2xl border-t border-line bg-white/95 p-4 backdrop-blur sm:flex-row sm:justify-end sm:px-8">
                    <a href="{{ route('landlord.properties.show', $house) }}" class="btn btn-secondary btn-lg">Cancel</a>
                    <button type="submit" id="saveBtn" class="btn btn-primary btn-lg">Save room</button>
                </div>
            </form>

            <aside class="self-start rounded-2xl bg-mist p-5 text-sm leading-relaxed text-muted">
                <h2 class="flex items-center gap-2 font-display text-base font-bold text-bay">
                    <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-info"/></svg>
                    Good to know
                </h2>
                <ul class="mt-3 space-y-3">
                    <li><span class="font-medium text-bay">Label it as tenants will.</span> A door number or letter works well.</li>
                    <li><span class="font-medium text-bay">Capacity is per room.</span> Renting by individual bed is not available yet.</li>
                    <li><span class="font-medium text-bay">Photos come next.</span> After saving, add photos on the room page. The first one becomes the cover.</li>
                </ul>
            </aside>
        </div>
    </div>

    <script>
        document.getElementById('roomForm').addEventListener('submit', function () {
            var b = document.getElementById('saveBtn');
            b.textContent = 'Saving…';
            setTimeout(function () { b.disabled = true; }, 0);
        });
    </script>
@endsection