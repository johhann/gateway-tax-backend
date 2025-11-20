<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
        $validationRules = (new StoreDocumentRequest)->rules();

        foreach ($validationRules as $field => $rules) {
            if (is_array($rules)) {
                $validationRules[$field] = array_filter($rules, function ($rule) {
                    return $rule !== 'required' && $rule !== 'required_if';
                });
            } else {
                $validationRules[$field] = str_replace('required', 'sometimes', $rules);
            }
        }

        return $validationRules;
    }
}
