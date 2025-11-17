<?php

namespace App\Http\Requests;

use App\Rules\StateValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'address_line_one' => ['required', 'string', 'max:255'],
            'address_line_two' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', new StateValidation],
            'zip_code' => ['required', 'string', 'max:20'],
            'work_phone' => ['required', 'string', 'max:20'],
            'home_phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'has_1099_misc' => ['required', 'boolean'],
            'description' => ['required', 'string'],
            'is_license_requirement' => ['required', 'boolean'],
            'has_business_license' => ['required', 'boolean'],
            'advertise_through' => ['nullable', Rule::in('Newspaper', 'Flyers', 'Personal Computer', 'Other')],
            'business_advertisement' => ['required_if:advertise_through,Other', 'nullable', 'string'],
            'records' => ['nullable', Rule::in('Accounting Records', 'Computer Records', 'Business Bank Accounts', 'Paid Invoices/Receipts', 'Business Stationery', 'Insurance', 'Advertising', 'Car/Truck Expense', 'Rental Expense', 'Other')],
            'other_record' => ['required_if:records,Other', 'nullable', 'string'],
            'file_taxed_for_tax_year' => ['required', 'boolean'],
            'profile_id' => ['required', 'exists:profiles,id'],
        ];
    }
}
