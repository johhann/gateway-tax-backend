<?php

namespace App\Http\Requests;

use App\Enums\DependantRelationship;
use App\Enums\FilingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLegalRequest extends FormRequest
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
            'profile_id' => ['required', 'exists:profiles,id'],
            'legal_city_id' => ['required', 'exists:legal_cities,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'social_security_number' => ['required', 'string', 'max:9', 'confirmed'],
            'filing_status' => ['required', Rule::in(FilingStatus::values())],
            'spouse_information' => ['nullable', 'array', Rule::requiredIf(function () {
                return $this->input('filing_status') === FilingStatus::MarriedFilingJointly->value || $this->input('filing_status') === FilingStatus::MarriedFilingSeparately->value || $this->input('filing_status') === FilingStatus::QualifyingWidower->value;
            })],
            'spouse_information.first_name' => ['required_with:spouse_information', 'string', 'max:100'],
            'spouse_information.last_name' => ['required_with:spouse_information', 'string', 'max:100'],
            'spouse_information.middle_name' => ['nullable', 'string', 'max:100'],
            'spouse_information.birth_date' => ['nullable', 'date'],
            'spouse_information.social_security_number' => ['required_with:spouse_information', 'string', 'max:9', 'confirmed'],
            'number_of_dependant' => ['nullable', 'integer', 'min:0'],
            'dependants' => ['nullable', 'array', Rule::requiredIf(function () {
                return $this->input('number_of_dependant') > 0;
            })],
            'dependants.*.id' => [
                'nullable',
                'integer',
                Rule::exists('dependants', 'id')
                    ->where('legal_id', $this->input('legal')),
            ],
            'dependants.*.first_name' => ['required_with:dependants', 'string', 'max:100'],
            'dependants.*.last_name' => ['required_with:dependants', 'string', 'max:100'],
            'dependants.*.middle_name' => ['nullable', 'string', 'max:100'],
            'dependants.*.date_of_birth' => ['nullable', 'date'],
            'dependants.*.social_security_number' => ['nullable', 'string', 'max:9', 'confirmed'],
            // 'dependants.*.occupation' => ['nullable', 'string', 'max:255'],
            'dependants.*.relationship' => ['nullable', Rule::in(DependantRelationship::cases())],
        ];
    }
}
