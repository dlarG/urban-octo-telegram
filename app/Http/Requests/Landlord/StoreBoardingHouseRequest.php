<?php
namespace App\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardingHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\BoardingHouse::class);
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:150'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'address_line'        => ['required', 'string', 'max:255'],
            'barangay'            => ['required', 'string', 'max:100'],
            'city'                => ['required', 'string', 'max:100'],
            'province'            => ['required', 'string', 'max:100'],
            'lat'                 => ['required', 'numeric', 'between:-90,90'],
            'lng'                 => ['required', 'numeric', 'between:-180,180'],
            'curfew_time'         => ['nullable', 'date_format:H:i'],
            'allows_cooking'      => ['boolean'],
            'gender_policy'       => ['required', 'in:male_only,female_only,mixed'],
            'water_supply_rating' => ['nullable', 'integer', 'between:1,5'],
            'is_sub_metered'      => ['boolean'],
            'amenities'           => ['nullable', 'array'],
            'amenities.*'         => ['integer', 'exists:amenities,id'],
        ];
    }
}