<?php

namespace App\Http\Requests;

use App\Enums\CollectionName;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
            'profile_id' => ['required', 'integer', 'exists:profiles,id'],
            'w2_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::W2);
            })],
            'misc_1099_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::MISC1099);
            })],
            'mortgage_statement_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::MortgageStatement);
            })],
            'tuition_statement_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::TuitionStatement);
            })],
            'shared_riders_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::SharedRiders);
            })],
            'misc_id' => ['nullable', 'integer', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::Misc);
            })],
        ];
    }
}
