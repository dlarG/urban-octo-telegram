@extends('layouts.landlord')

@section('content')
    @include('landlord._ui')

    @php
        $price     = (float) $room->base_price_monthly;
        $priceText = number_format($price, fmod($price, 1) == 0.0 ? 0 : 2);
        $images    = $room->propertyImages;
    @endphp

    <div class="rs-page mx-auto max-w-6xl">
        <a href="{{ route('landlord.properties.show', $house) }}"
           class="inline-flex items-center gap-1 text-sm font-medium text-muted hover:text-bay">
            <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-back"/></svg>
            {{ $house->name }}
        </a>

        @if (session('status'))
            <div class="mt-4 flex items-start gap-3 rounded-lg bg-mist px-4 py-3 text-sm text-bay" role="status">
                <svg viewBox="0 0 24 24" class="ico mt-0.5 text-sea" style="width:1.1rem;height:1.1rem"><use href="#l-check-circle"/></svg>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        {{-- ================= Header ================= --}}
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-display text-3xl font-bold text-bay">{{ $room->room_label }}</h1>
                    @include('landlord.rooms._status-badge', ['status' => $room->status])
                </div>
                <p class="mt-1 text-muted">
                    {{ ucfirst($room->room_type->value) }} room for {{ $room->capacity }} {{ \Illuminate\Support\Str::plural('person', $room->capacity) }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('landlord.properties.rooms.edit', [$house, $room]) }}" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-pencil"/></svg>
                    Edit
                </a>
                <button type="button" class="btn btn-danger"
                        data-confirm-form="deleteRoomForm"
                        data-confirm-title="Delete {{ $room->room_label }}?"
                        data-confirm-text="This room will be removed from {{ $house->name }}. This cannot be undone."
                        data-confirm-label="Delete room">
                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-trash"/></svg>
                    Delete
                </button>
                <form id="deleteRoomForm" method="POST" class="hidden"
                      action="{{ route('landlord.properties.rooms.destroy', [$house, $room]) }}">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">

            {{-- ================= Details ================= --}}
            <aside class="lg:order-2">
                <div class="rounded-2xl border border-line bg-white p-5">
                    <p class="text-sm text-muted">Monthly rent</p>
                    <p class="mt-1">
                        <span class="font-display text-3xl font-bold text-bay">₱{{ $priceText }}</span>
                        <span class="text-muted">/ month</span>
                    </p>

                    <dl class="mt-5 divide-y divide-[#D9E3E2] border-t border-line text-sm">
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-muted">Room type</dt>
                            <dd class="font-medium text-bay">{{ ucfirst($room->room_type->value) }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-muted">Capacity</dt>
                            <dd class="font-medium text-bay">{{ $room->capacity }}</dd>
                        </div>
                        @foreach ([['Own bathroom', $room->has_own_bathroom, 'droplet'], ['Aircon', $room->has_aircon, 'wind']] as [$label, $has, $icon])
                            <div class="flex items-center justify-between py-3">
                                <dt class="flex items-center gap-2 text-muted">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-{{ $icon }}"/></svg>{{ $label }}
                                </dt>
                                <dd class="{{ $has ? 'font-medium text-sea' : 'text-muted' }}">{{ $has ? 'Included' : 'Not included' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </aside>

            {{-- ================= Photos ================= --}}
            <section class="lg:col-span-2 lg:order-1" aria-labelledby="photos-heading">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 id="photos-heading" class="font-display text-xl font-bold text-bay">
                            Photos <span class="font-sans text-base font-normal text-muted">({{ $images->count() }})</span>
                        </h2>
                        <p class="mt-1 text-sm text-muted">The cover photo is what renters see first.</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($images as $img)
                        <div class="group relative aspect-[4/3] overflow-hidden rounded-xl border border-line bg-mist">
                            <img src="{{ Storage::url($img->path) }}" alt="Photo {{ $loop->iteration }} of {{ $room->room_label }}"
                                 loading="lazy" class="h-full w-full object-cover">

                            @if ($img->is_primary)
                                <span class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-md bg-white/95 px-2 py-1 text-xs font-semibold text-bay shadow-sm">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:.85rem;height:.85rem"><use href="#l-image"/></svg>Cover
                                </span>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/65 to-transparent p-2 pt-8 sm:opacity-0 sm:transition-opacity sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                                @unless ($img->is_primary)
                                    <form method="POST" action="{{ route('landlord.images.primary', $img) }}">
                                        @csrf @method('PATCH')
                                        <button class="rounded-md bg-white/95 px-2.5 py-1.5 text-xs font-medium text-bay hover:bg-white">Set as cover</button>
                                    </form>
                                @else
                                    <span></span>
                                @endunless

                                <button type="button" aria-label="Delete photo {{ $loop->iteration }}"
                                        class="grid h-8 w-8 place-items-center rounded-md bg-white/95 text-danger hover:bg-white"
                                        data-confirm-form="deleteImage{{ $img->id }}"
                                        data-confirm-title="Delete this photo?"
                                        data-confirm-text="{{ $img->is_primary && $images->count() > 1 ? 'This is the cover photo. The next photo will become the cover.' : 'This cannot be undone.' }}"
                                        data-confirm-label="Delete photo">
                                    <svg viewBox="0 0 24 24" class="ico" style="width:1rem;height:1rem"><use href="#l-trash"/></svg>
                                </button>
                                <form id="deleteImage{{ $img->id }}" method="POST" class="hidden"
                                      action="{{ route('landlord.images.destroy', $img) }}">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>
                    @endforeach

                    {{-- Upload tile --}}
                    <form id="uploadForm" method="POST" enctype="multipart/form-data"
                          action="{{ route('landlord.properties.rooms.images.store', [$house, $room]) }}"
                          class="{{ $images->isEmpty() ? 'col-span-2 sm:col-span-3' : '' }}">
                        @csrf
                        <input id="room-image-input" type="file" name="image" accept="image/*" required class="peer sr-only">
                        <label for="room-image-input"
                               class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-[#9BB5B3] bg-white px-4 text-center text-muted transition-colors hover:border-[#177E89] hover:bg-mist hover:text-bay peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89] {{ $images->isEmpty() ? 'py-14' : 'aspect-[4/3]' }}">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-mist text-sea">
                                <svg viewBox="0 0 24 24" class="ico"><use href="#l-upload"/></svg>
                            </span>
                            <span data-upload-text class="text-sm font-medium">
                                {{ $images->isEmpty() ? 'Add your first photo' : 'Add photo' }}
                            </span>
                            @if ($images->isEmpty())
                                <span class="max-w-xs text-sm">The first photo you add becomes the cover.</span>
                            @endif
                        </label>
                    </form>
                </div>
            </section>
        </div>
    </div>

    {{-- ================= Confirm dialog ================= --}}
    <dialog id="confirmDialog" class="rs-page m-auto w-[calc(100%-2rem)] max-w-sm overflow-hidden rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-black/40">
        <div class="p-6">
            <h2 id="confirmTitle" class="font-display text-xl font-bold text-bay"></h2>
            <p id="confirmText" class="mt-2 text-sm leading-relaxed text-muted"></p>
        </div>
        <div class="flex justify-end gap-2 border-t border-line bg-mist px-6 py-4">
            <button type="button" id="confirmCancel" class="btn btn-secondary">Cancel</button>
            <button type="button" id="confirmOk" class="btn btn-danger-solid">Delete</button>
        </div>
    </dialog>

    <script>
        (function () {
            var dlg = document.getElementById('confirmDialog');
            var title = document.getElementById('confirmTitle');
            var text = document.getElementById('confirmText');
            var ok = document.getElementById('confirmOk');
            var pending = null;

            document.querySelectorAll('[data-confirm-form]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    pending = document.getElementById(btn.dataset.confirmForm);
                    title.textContent = btn.dataset.confirmTitle;
                    text.textContent = btn.dataset.confirmText;
                    ok.textContent = btn.dataset.confirmLabel || 'Delete';
                    dlg.showModal();
                });
            });
            ok.addEventListener('click', function () {
                ok.disabled = true;
                if (pending) pending.submit();
            });
            document.getElementById('confirmCancel').addEventListener('click', function () { dlg.close(); });
            dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });

            // Upload as soon as a photo is chosen
            var input = document.getElementById('room-image-input');
            input.addEventListener('change', function () {
                if (!input.files.length) return;
                var form = document.getElementById('uploadForm');
                form.querySelector('[data-upload-text]').textContent = 'Uploading…';
                form.classList.add('opacity-60', 'pointer-events-none');
                form.submit();
            });
        })();
    </script>
@endsection