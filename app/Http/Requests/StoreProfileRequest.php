<?php

namespace App\Http\Requests;

use App\Enums\InformationSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|unique:users,phone',
            'zip_code' => 'required|numeric|string',
            'tax_station_id' => ['required', Rule::exists('tax_stations', 'id')->where(function ($query) {
                $query->where('status', true);
            })],
            'hear_from' => ['required', Rule::in(InformationSource::values())],
            'occupation' => 'required|string',
            'self_employment_income' => 'required|boolean',
            'ssn' => 'required|string|max:9',
        ];
    }
}
