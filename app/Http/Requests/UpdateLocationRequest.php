<?php

namespace App\Http\Requests;

use App\Models\PhysicalLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $location = $this->route('location');

        return $this->user()?->can('update', $location) ?? false;
    }

    public function rules(): array
    {
        $location = $this->route('location');

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('physical_locations', 'code')->ignore($location)],
            'province' => ['required', 'string', Rule::in(PhysicalLocation::PROVINCES)],
            'district' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'google_map_embed_url' => ['nullable', 'string', 'max:1000'],
            'max_room_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
