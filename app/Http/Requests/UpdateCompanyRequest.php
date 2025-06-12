<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'country_id'           => 'nullable|exists:countries,id',
            'city_id'              => 'nullable|exists:cities,id',
            'address'              => 'nullable|string|max:255',
            'postal_code'          => 'nullable|string|max:20',
            'phone_code'           => 'nullable|string|max:10',
            'name'                 => 'nullable|string|max:255',
            'logo'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tax_number'           => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'email'                => 'nullable|email|max:255|unique:companies,email,' . Auth::user()->id . ',user_id',
            'is_finished_profile' => 'boolean',
        ];
    }
}
