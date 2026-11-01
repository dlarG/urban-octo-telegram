{{-- resources/views/landlord/properties/_form.blade.php --}}
@php
    $isEdit   = isset($house);
    $selected = old('amenities', isset($house) ? $house->amenities->pluck('id')->all() : []);
@endphp

<style>
    /* Checkbox tiles (used for the two toggles and the amenities) */
    .opt-card { display: flex; align-items: center; gap: .625rem; border: 1px solid var(--line); background: #fff;
                border-radius: .5rem; padding: .7rem .875rem; font-size: .875rem; color: var(--ink);
                transition: background-color .15s, border-color .15s; }
    .opt:hover .opt-card { background: var(--mist); }
    .opt-box { display: grid; place-items: center; flex: none; width: 1.25rem; height: 1.25rem; border: 1.5px solid #9BB5B3;
               border-radius: .3rem; background: #fff; color: #fff; transition: background-color .15s, border-color .15s; }
    .opt-box svg { width: .8rem; height: .8rem; stroke-width: 3; opacity: 0; }
    .opt input:checked + .opt-card { border-color: var(--sea); background: #F1F8F7; }
    .opt input:checked + .opt-card .opt-box { background: var(--sea); border-color: var(--sea); }
    .opt input:checked + .opt-card .opt-box svg { opacity: 1; }
    .opt input:focus-visible + .opt-card { outline: 2px solid var(--sea); outline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) { .opt-card, .opt-box { transition: none; } }
</style>

<div class="space-y-5">

    {{-- ================= Basics ================= --}}
    <section class="rounded-2xl border border-line bg-white" aria-labelledby="sec-basics">
        <div class="grid gap-x-10 gap-y-5 p-5 sm:p-6 lg:grid-cols-[14rem_1fr]">
            <div>
                <h2 id="sec-basics" class="font-display text-lg font-bold text-bay">Basics</h2>
                <p class="mt-1 text-sm text-muted">How your property is named and described to renters.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="f-name" class="mb-1.5 block text-sm font-medium text-ink">Property name <span class="text-danger" aria-hidden="true">*</span></label>
                    <input id="f-name" name="name" type="text" required
                           value="{{ old('name', $house->name ?? '') }}"
                           class="field" @error('name') aria-invalid="true" @enderror>
                </div>

                <div>
                    <label for="f-description" class="mb-1.5 block text-sm font-medium text-ink">Description</label>
                    <textarea id="f-description" name="description" rows="5"
                              class="field !h-auto !py-3 leading-relaxed" @error('description') aria-invalid="true" @enderror>{{ old('description', $house->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Location ================= --}}
    <section class="rounded-2xl border border-line bg-white" aria-labelledby="sec-location">
        <div class="grid gap-x-10 gap-y-5 p-5 sm:p-6 lg:grid-cols-[14rem_1fr]">
            <div>
                <h2 id="sec-location" class="font-display text-lg font-bold text-bay">Location</h2>
                <p class="mt-1 text-sm text-muted">The address and the exact spot on the map.</p>
            </div>

            <div class="space-y-6">
                <fieldset class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <legend class="sr-only">Address</legend>
                    <div class="sm:col-span-3">
                        <label for="f-address" class="mb-1.5 block text-sm font-medium text-ink">Address line <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="f-address" name="address_line" type="text" required
                               value="{{ old('address_line', $house->address_line ?? '') }}"
                               class="field" @error('address_line') aria-invalid="true" @enderror>
                    </div>
                    <div>
                        <label for="f-barangay" class="mb-1.5 block text-sm font-medium text-ink">Barangay <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="f-barangay" name="barangay" type="text" required
                               value="{{ old('barangay', $house->barangay ?? 'Poblacion') }}"
                               class="field" @error('barangay') aria-invalid="true" @enderror>
                    </div>
                    <div>
                        <label for="f-city" class="mb-1.5 block text-sm font-medium text-ink">City <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="f-city" name="city" type="text" required
                               value="{{ old('city', $house->city ?? 'Sogod') }}"
                               class="field" @error('city') aria-invalid="true" @enderror>
                    </div>
                    <div>
                        <label for="f-province" class="mb-1.5 block text-sm font-medium text-ink">Province <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="f-province" name="province" type="text" required
                               value="{{ old('province', $house->province ?? 'Southern Leyte') }}"
                               class="field" @error('province') aria-invalid="true" @enderror>
                    </div>
                </fieldset>

                {{-- Map picker (ids and data attributes are used by your map script) --}}
                <div>
                    <p class="text-sm font-medium text-ink">Pin location <span class="text-danger" aria-hidden="true">*</span></p>
                    <p class="mt-0.5 text-sm text-muted">Click on the map to place the pin, or drag it.</p>

                    {{-- `isolate` keeps the map's layers from covering the sticky header and menus --}}
                    <div id="map-picker" class="isolate mt-3 h-80 overflow-hidden rounded-xl border border-line"
                         data-lat="{{ old('lat', $house->lat ?? config('rentstreet.center_lat')) }}"
                         data-lng="{{ old('lng', $house->lng ?? config('rentstreet.center_lng')) }}"></div>

                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label for="lat" class="mb-1 block text-xs font-medium text-muted">Latitude</label>
                            <input id="lat" name="lat" type="text" required readonly
                                   value="{{ old('lat', $house->lat ?? config('rentstreet.center_lat')) }}"
                                   class="field !h-10 !text-sm tabular-nums text-muted" style="background: var(--mist)"
                                   @error('lat') aria-invalid="true" @enderror>
                        </div>
                        <div>
                            <label for="lng" class="mb-1 block text-xs font-medium text-muted">Longitude</label>
                            <input id="lng" name="lng" type="text" required readonly
                                   value="{{ old('lng', $house->lng ?? config('rentstreet.center_lng')) }}"
                                   class="field !h-10 !text-sm tabular-nums text-muted" style="background: var(--mist)"
                                   @error('lng') aria-invalid="true" @enderror>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= House rules ================= --}}
    <section class="rounded-2xl border border-line bg-white" aria-labelledby="sec-rules">
        <div class="grid gap-x-10 gap-y-5 p-5 sm:p-6 lg:grid-cols-[14rem_1fr]">
            <div>
                <h2 id="sec-rules" class="font-display text-lg font-bold text-bay">House rules</h2>
                <p class="mt-1 text-sm text-muted">Policies and utilities for the whole property.</p>
            </div>

            <div class="space-y-5">
                <fieldset class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <legend class="sr-only">Policies</legend>
                    <div>
                        <label for="f-gender" class="mb-1.5 block text-sm font-medium text-ink">Gender policy <span class="text-danger" aria-hidden="true">*</span></label>
                        <select id="f-gender" name="gender_policy" required class="field" @error('gender_policy') aria-invalid="true" @enderror>
                            @foreach (['mixed', 'male_only', 'female_only'] as $opt)
                                <option value="{{ $opt }}" @selected(old('gender_policy', $house->gender_policy ?? 'mixed') === $opt)>
                                    {{ ucfirst(str_replace('_', ' ', $opt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="f-curfew" class="mb-1.5 block text-sm font-medium text-ink">Curfew time</label>
                        <input id="f-curfew" name="curfew_time" type="time"
                               value="{{ old('curfew_time', $house->curfew_time ?? '') }}"
                               class="field" @error('curfew_time') aria-invalid="true" @enderror>
                    </div>
                    <div>
                        <label for="f-water" class="mb-1.5 block text-sm font-medium text-ink">Water supply rating (1–5)</label>
                        <input id="f-water" name="water_supply_rating" type="number" min="1" max="5"
                               value="{{ old('water_supply_rating', $house->water_supply_rating ?? '') }}"
                               class="field" @error('water_supply_rating') aria-invalid="true" @enderror>
                    </div>
                </fieldset>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="opt relative block cursor-pointer">
                        <input type="hidden" name="allows_cooking" value="0">
                        <input type="checkbox" name="allows_cooking" value="1" class="sr-only"
                               @checked(old('allows_cooking', $house->allows_cooking ?? false))>
                        <span class="opt-card">
                            <span class="opt-box" aria-hidden="true"><svg viewBox="0 0 24 24" class="ico"><use href="#l-check"/></svg></span>
                            Allows cooking
                        </span>
                    </label>

                    <label class="opt relative block cursor-pointer">
                        <input type="hidden" name="is_sub_metered" value="0">
                        <input type="checkbox" name="is_sub_metered" value="1" class="sr-only"
                               @checked(old('is_sub_metered', $house->is_sub_metered ?? false))>
                        <span class="opt-card">
                            <span class="opt-box" aria-hidden="true"><svg viewBox="0 0 24 24" class="ico"><use href="#l-check"/></svg></span>
                            Sub-metered utilities
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Amenities ================= --}}
    <section class="rounded-2xl border border-line bg-white" aria-labelledby="sec-amenities">
        <div class="grid gap-x-10 gap-y-5 p-5 sm:p-6 lg:grid-cols-[14rem_1fr]">
            <div>
                <h2 id="sec-amenities" class="font-display text-lg font-bold text-bay">Amenities</h2>
                <p class="mt-1 text-sm text-muted">Select everything your property offers.</p>
            </div>

            <fieldset class="space-y-6">
                <legend class="sr-only">Amenities</legend>
                @foreach ($amenities as $category => $items)
                    <div>
                        <p class="mb-2.5 text-sm font-semibold text-ink">{{ $category }}</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($items as $amenity)
                                <label class="opt relative block cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="sr-only"
                                           @checked(in_array($amenity->id, $selected))>
                                    <span class="opt-card">
                                        <span class="opt-box" aria-hidden="true"><svg viewBox="0 0 24 24" class="ico"><use href="#l-check"/></svg></span>
                                        {{ $amenity->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </fieldset>
        </div>
    </section>
</div>