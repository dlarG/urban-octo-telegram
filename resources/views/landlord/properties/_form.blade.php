@php
    $isEdit = isset($house);
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-gray-700">Property name *</label>
        <input name="name" type="text" required
               value="{{ old('name', $house->name ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $house->description ?? '') }}</textarea>
    </div>

    <fieldset class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-3">
            <label class="block text-sm font-medium text-gray-700">Address line *</label>
            <input name="address_line" type="text" required
                   value="{{ old('address_line', $house->address_line ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Barangay *</label>
            <input name="barangay" type="text" required
                   value="{{ old('barangay', $house->barangay ?? 'Poblacion') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">City *</label>
            <input name="city" type="text" required
                   value="{{ old('city', $house->city ?? 'Sogod') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Province *</label>
            <input name="province" type="text" required
                   value="{{ old('province', $house->province ?? 'Southern Leyte') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </fieldset>

    {{-- Map picker --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Location *</label>
        <p class="text-xs text-gray-500 mt-0.5">Click on the map to place the pin, or drag it.</p>
        <div id="map-picker" class="mt-2 h-72 rounded-md border" data-lat="{{ old('lat', $house->lat ?? config('rentstreet.center_lat')) }}" data-lng="{{ old('lng', $house->lng ?? config('rentstreet.center_lng')) }}"></div>
        <div class="grid grid-cols-2 gap-4 mt-2">
            <input id="lat" name="lat" type="text" required readonly
                   value="{{ old('lat', $house->lat ?? config('rentstreet.center_lat')) }}"
                   class="block w-full rounded-md bg-gray-50 border-gray-300 text-sm">
            <input id="lng" name="lng" type="text" required readonly
                   value="{{ old('lng', $house->lng ?? config('rentstreet.center_lng')) }}"
                   class="block w-full rounded-md bg-gray-50 border-gray-300 text-sm">
        </div>
    </div>

    <fieldset class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Gender policy *</label>
            <select name="gender_policy" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (['mixed', 'male_only', 'female_only'] as $opt)
                    <option value="{{ $opt }}" @selected(old('gender_policy', $house->gender_policy ?? 'mixed') === $opt)>
                        {{ ucfirst(str_replace('_', ' ', $opt)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Curfew time</label>
            <input name="curfew_time" type="time"
                   value="{{ old('curfew_time', $house->curfew_time ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Water supply rating (1–5)</label>
            <input name="water_supply_rating" type="number" min="1" max="5"
                   value="{{ old('water_supply_rating', $house->water_supply_rating ?? '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="flex items-end gap-4">
            <label class="inline-flex items-center">
                <input type="hidden" name="allows_cooking" value="0">
                <input type="checkbox" name="allows_cooking" value="1"
                       @checked(old('allows_cooking', $house->allows_cooking ?? false))
                       class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm text-gray-700">Allows cooking</span>
            </label>
            <label class="inline-flex items-center">
                <input type="hidden" name="is_sub_metered" value="0">
                <input type="checkbox" name="is_sub_metered" value="1"
                       @checked(old('is_sub_metered', $house->is_sub_metered ?? false))
                       class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm text-gray-700">Sub-metered utilities</span>
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend class="block text-sm font-medium text-gray-700">Amenities</legend>
        @php $selected = old('amenities', isset($house) ? $house->amenities->pluck('id')->all() : []); @endphp
        <div class="mt-2 space-y-3">
            @foreach ($amenities as $category => $items)
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ $category }}</div>
                    <div class="mt-1 flex flex-wrap gap-x-4 gap-y-2">
                        @foreach ($items as $amenity)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                       @checked(in_array($amenity->id, $selected))
                                       class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </fieldset>
</div>