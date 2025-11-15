<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRequestRequest extends FormRequest
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
            'tax_year' => ['required', 'date_format:Y', 'before_or_equal:'.date('Y')],
            'full_name' => ['required', 'string', 'max:255'],
            'ssn' => ['required', 'string', 'max:11', 'unique:tax_requests,ssn'],
            'specific_request' => ['nullable', 'string'],
        ];
    }
}
