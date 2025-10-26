<?php

namespace App\Http\Requests;

use App\Enums\InformationSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date',
            'phone' => 'sometimes|string',
            'zip' => 'sometimes|string',
            //            "taxes_last_year" => ["sometimes", Rule::in("Gateway Tax Service", "Jackson Hewitt", "H&R Block", "Liberty", "Turbo", "Other")],
            'hear_from' => ['sometimes', Rule::in(InformationSource::values())],
            'occupation' => 'sometimes|string',
            'self_employment_income' => 'sometimes|boolean',
        ];
    }
}
