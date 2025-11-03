<?php

namespace App\Http\Requests;

use App\Enums\LicenseType;
use App\Rules\StateValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIdentificationRequest extends FormRequest
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
        return (function () {
            return array_merge((new StoreAddressRequest)->rules(), [
                'license_type' => ['required', 'string', Rule::in(LicenseType::values())],
                'license_number' => 'required|string',
                'issuing_state' => ['required', 'string', new StateValidation],
                'license_issue_date' => ['required', 'date', 'before:license_expiration_date', 'before:today'],
                'license_expiration_date' => ['required', 'date', 'after:license_issue_date', 'after_or_equal:today'],
                'license_front_image_id' => ['required', 'exists:attachments,id'],
                'license_back_image_id' => ['required', 'exists:attachments,id'],
            ]);
        })();
    }
}
