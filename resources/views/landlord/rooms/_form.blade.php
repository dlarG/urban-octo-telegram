@php
    $isEdit   = isset($room);
    $roomType = old('room_type', $isEdit ? $room->room_type->value : 'private');
    $status   = old('status', $isEdit ? $room->status->value : 'available');
    $capacity = old('capacity', $isEdit ? $room->capacity : 1);
    $price    = old('base_price_monthly', $isEdit ? $room->base_price_monthly : '');
    $bathroom = old('has_own_bathroom', $isEdit ? $room->has_own_bathroom : false);
    $aircon   = old('has_aircon', $isEdit ? $room->has_aircon : false);

    $types = [
        'private' => ['Private', 'One tenant or one household', 'user'],
        'shared'  => ['Shared', 'Several tenants share the room', 'users'],
    ];
    $statuses = [
        'available'   => ['Available', 'Open for applications', 'bg-[#0F6B5A]'],
        'full'        => ['Full', 'No vacancies right now', 'bg-[#0B3C49]'],
        'maintenance' => ['Maintenance', 'Temporarily unavailable', 'bg-[#C48A1A]'],
        'delisted'    => ['Delisted', 'Removed from your listings', 'bg-[#8A9A9E]'],
    ];
    $features = [
        'has_own_bathroom' => ['Own bathroom', 'Private toilet and bath', 'droplet', $bathroom],
        'has_aircon'       => ['Aircon', 'Air-conditioned room', 'wind', $aircon],
    ];
@endphp

<div class="space-y-8">

    {{-- ================= Basics ================= --}}
    <section aria-labelledby="sec-basics">
        <h2 id="sec-basics" class="font-display text-lg font-bold text-bay">Basics</h2>
        <p class="mt-0.5 text-sm text-muted">How tenants will see and find this room.</p>

        <div class="mt-5 space-y-5">
            <div>
                <label for="room_label" class="block text-sm font-medium text-bay">Room label</label>
                <input id="room_label" name="room_label" type="text" required maxlength="100"
                       value="{{ old('room_label', $isEdit ? $room->room_label : '') }}"
                       placeholder="e.g. Room 1, Room A, Upper bunk"
                       class="field mt-1.5"
                       @error('room_label') aria-invalid="true" aria-describedby="room_label-error" @enderror>
                @error('room_label')
                    <p id="room_label-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <fieldset @error('room_type') data-invalid @enderror>
                <legend class="mb-1.5 text-sm font-medium text-bay">Room type</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($types as $value => [$label, $hint, $icon])
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="room_type" value="{{ $value }}" class="peer sr-only" @checked($roomType === $value)>
                            <span class="choice-card flex items-start gap-3 rounded-xl border border-line bg-white p-4 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                                <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-white text-sea ring-1 ring-[#D9E3E2]">
                                    <svg viewBox="0 0 24 24" class="ico"><use href="#l-{{ $icon }}"/></svg>
                                </span>
                                <span class="pr-7">
                                    <span class="block font-semibold text-bay">{{ $label }}</span>
                                    <span class="mt-0.5 block text-sm text-muted">{{ $hint }}</span>
                                </span>
                            </span>
                            <span class="pointer-events-none absolute right-3.5 top-3.5 grid h-5 w-5 place-items-center rounded-full border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                                <svg viewBox="0 0 24 24" class="ico" style="width:.75rem;height:.75rem;stroke-width:3"><use href="#l-check"/></svg>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('room_type')
                    <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
                @enderror
            </fieldset>
        </div>
    </section>

    {{-- ================= Capacity and price ================= --}}
    <section aria-labelledby="sec-pricing" class="border-t border-line pt-8">
        <h2 id="sec-pricing" class="font-display text-lg font-bold text-bay">Capacity and price</h2>
        <p class="mt-0.5 text-sm text-muted">Rent is charged per month for the whole room.</p>

        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
                <label for="capacity" class="block text-sm font-medium text-bay">Capacity</label>
                <div data-stepper
                     class="mt-1.5 inline-flex h-12 items-center rounded-md border bg-white focus-within:border-[#177E89] focus-within:shadow-[0_0_0_3px_rgb(23_126_137/0.18)] {{ $errors->has('capacity') ? 'border-[#A32A2A]' : 'border-line' }}">
                    <button type="button" data-delta="-1" aria-label="Decrease capacity"
                            class="grid h-full w-12 place-items-center rounded-l-md text-bay hover:bg-mist">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-minus"/></svg>
                    </button>
                    <input id="capacity" name="capacity" type="number" inputmode="numeric" min="1" max="20" required
                           value="{{ $capacity }}"
                           class="h-full w-14 border-0 bg-transparent p-0 text-center text-base focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                           @error('capacity') aria-invalid="true" aria-describedby="capacity-error" @enderror>
                    <button type="button" data-delta="1" aria-label="Increase capacity"
                            class="grid h-full w-12 place-items-center rounded-r-md text-bay hover:bg-mist">
                        <svg viewBox="0 0 24 24" class="ico"><use href="#l-plus"/></svg>
                    </button>
                </div>
                <p class="mt-1.5 text-sm text-muted">How many people it fits (1 to 20).</p>
                @error('capacity')
                    <p id="capacity-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div>
                <label for="base_price_monthly" class="block text-sm font-medium text-bay">Monthly price</label>
                <div class="relative mt-1.5">
                    <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-muted" aria-hidden="true">₱</span>
                    <input id="base_price_monthly" name="base_price_monthly" type="number" inputmode="decimal" step="0.01" min="0" required
                           value="{{ $price }}" placeholder="0.00" class="field pl-9"
                           @error('base_price_monthly') aria-invalid="true" aria-describedby="price-error" @enderror>
                </div>
                <p class="mt-1.5 text-sm text-muted">Pesos per month.</p>
                @error('base_price_monthly')
                    <p id="price-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-danger">
                        <svg viewBox="0 0 24 24" class="ico mt-0.5" style="width:1rem;height:1rem"><use href="#l-alert"/></svg><span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>
    </section>

    {{-- ================= Features ================= --}}
    <section aria-labelledby="sec-features" class="border-t border-line pt-8">
        <h2 id="sec-features" class="font-display text-lg font-bold text-bay">What is in the room</h2>
        <p class="mt-0.5 text-sm text-muted">Turn on whatever applies.</p>

        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            @foreach ($features as $name => [$label, $hint, $icon, $checked])
                <label class="relative block cursor-pointer">
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked($checked)>
                    <span class="choice-card flex items-start gap-3 rounded-xl border border-line bg-white p-4 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-white text-sea ring-1 ring-[#D9E3E2]">
                            <svg viewBox="0 0 24 24" class="ico"><use href="#l-{{ $icon }}"/></svg>
                        </span>
                        <span class="pr-7">
                            <span class="block font-semibold text-bay">{{ $label }}</span>
                            <span class="mt-0.5 block text-sm text-muted">{{ $hint }}</span>
                        </span>
                    </span>
                    <span class="pointer-events-none absolute right-3.5 top-3.5 grid h-5 w-5 place-items-center rounded-md border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                        <svg viewBox="0 0 24 24" class="ico" style="width:.75rem;height:.75rem;stroke-width:3"><use href="#l-check"/></svg>
                    </span>
                </label>
            @endforeach
        </div>
    </section>

    {{-- ================= Status ================= --}}
    <section aria-labelledby="sec-status" class="border-t border-line pt-8">
        <h2 id="sec-status" class="font-display text-lg font-bold text-bay">Availability</h2>
        <p class="mt-0.5 text-sm text-muted">You can change this any time.</p>

        <fieldset class="mt-5" @error('status') data-invalid @enderror>
            <legend class="sr-only">Room status</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($statuses as $value => [$label, $hint, $dot])
                    <label class="relative block cursor-pointer">
                        <input type="radio" name="status" value="{{ $value }}" class="peer sr-only" @checked($status === $value)>
                        <span class="choice-card flex items-start gap-3 rounded-xl border border-line bg-white p-4 hover:bg-mist peer-checked:border-[#0B3C49] peer-checked:bg-mist peer-checked:shadow-[inset_0_0_0_1px_#0B3C49] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#177E89]">
                            <span class="mt-1.5 h-2.5 w-2.5 flex-none rounded-full {{ $dot }}" aria-hidden="true"></span>
                            <span class="pr-7">
                                <span class="block font-semibold text-bay">{{ $label }}</span>
                                <span class="mt-0.5 block text-sm text-muted">{{ $hint }}</span>
                            </span>
                        </span>
                        <span class="pointer-events-none absolute right-3.5 top-3.5 grid h-5 w-5 place-items-center rounded-full border border-[#9BB5B3] bg-white text-white peer-checked:border-[#0B3C49] peer-checked:bg-[#0B3C49]">
                            <svg viewBox="0 0 24 24" class="ico" style="width:.75rem;height:.75rem;stroke-width:3"><use href="#l-check"/></svg>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('status')
                <p class="mt-1.5 text-sm text-danger">{{ $message }}</p>
            @enderror
        </fieldset>
    </section>
</div>

<script>
    // Capacity stepper
    document.querySelectorAll('[data-stepper]').forEach(function (box) {
        var input = box.querySelector('input');
        box.querySelectorAll('[data-delta]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var min = +input.min || 1, max = +input.max || 20;
                var next = (parseInt(input.value, 10) || min) + parseInt(btn.dataset.delta, 10);
                input.value = Math.max(min, Math.min(max, next));
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    });
</script>