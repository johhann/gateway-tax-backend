<?php

namespace App\Http\Requests;

use App\Enums\CollectionName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttachmentRequest extends FormRequest
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
            'collection_name' => ['required', Rule::in(array_filter(
                CollectionName::values(), fn ($value) => $value !== CollectionName::PDFAttachments->value
            ))],
            'file' => 'required|file|max:20480',
            'metadata' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        $allowed = array_filter(
            CollectionName::values(),
            fn ($value) => $value !== CollectionName::PDFAttachments->value
        );

        return [
            'collection_name.in' => 'The selected collection_name is invalid. Allowed values: '.implode(', ', $allowed),
        ];
    }
}
