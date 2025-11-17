<?php

namespace App\Http\Requests;

use App\Enums\LicenseType;
use App\Rules\StateValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIdentificationRequest extends FormRequest
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
        return array_merge((new UpdateAddressRequest)->rules(), [
            'license_type' => ['sometimes', 'required', 'string', Rule::in(LicenseType::values())],
            'license_number' => 'sometimes|required|string',
            'issuing_state' => ['sometimes', 'required', 'string', new StateValidation],
            'license_issue_date' => ['sometimes', 'required', 'date', 'before:license_expiration_date', 'before:today'],
            'license_expiration_date' => ['sometimes', 'required', 'date', 'after:license_issue_date', 'after_or_equal:today'],
            'profile_id' => 'required|exists:profiles,id',
        ]);
    }
}
