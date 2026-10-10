<?php
namespace App\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isLandlord();
    }

    public function rules(): array
    {
        return [
            'room_label'          => ['required', 'string', 'max:50'],
            'room_type'           => ['required', 'in:private,shared'],
            'capacity'            => ['required', 'integer', 'between:1,20'],
            'base_price_monthly'  => ['required', 'numeric', 'min:0', 'max:999999'],
            'has_own_bathroom'    => ['boolean'],
            'has_aircon'          => ['boolean'],
            'status'              => ['required', 'in:available,full,maintenance,delisted'],
        ];
    }
}