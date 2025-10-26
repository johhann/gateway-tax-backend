<?php

namespace App\Http\Requests;

use App\Rules\StateValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'address' => 'sometimes|required|string',
            'apt' => 'sometimes|nullable|string',
            'zip_code' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'state' => ['sometimes', 'required', 'string', new StateValidation],
        ];
    }
}
