<?php

namespace App\Http\Requests;

use App\Rules\StateValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'address' => 'required|string',
            'apt' => 'nullable|string',
            'city' => 'required|string',
            'state' => ['required', 'string', new StateValidation],
        ];
    }
}
