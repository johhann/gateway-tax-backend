<?php

namespace App\Http\Requests;

use App\Enums\CollectionName;
use App\Enums\DirectDepositAccountType;
use App\Enums\RefundFee;
use App\Enums\RefundMethod;
use App\Enums\RefundType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
        $rules = [
            'type' => [
                'required_without_all:refund_method,refund_fee',
                'string',
                Rule::in(RefundType::values()),
            ],
            'refund_method' => [
                'required_without_all:type,refund_fee',
                'string',
                Rule::in(RefundMethod::values()),
            ],
            'refund_fee' => [
                'required_without_all:type,refund_method',
                'string',
                Rule::in(RefundFee::values()),
            ],
            'profile_id' => ['required', 'exists:profiles,id'],
        ];

        // Conditionally add rules for direct deposit
        if ($this->input('refund_method') === RefundMethod::DirectDeposit->value ||
            $this->input('refund_fee') === RefundFee::DirectDeposit->value) {
            $rules['direct_deposit_info'] = ['required', 'array'];
            $rules['direct_deposit_info.bank_name'] = ['required', 'string'];
            $rules['direct_deposit_info.account_type'] = [
                'required',
                'string',
                Rule::in(DirectDepositAccountType::values()),
            ];
            $rules['direct_deposit_info.account_holder'] = ['required', 'string'];
            $rules['direct_deposit_info.routing_number'] = ['required', 'string'];
            $rules['direct_deposit_info.account_number'] = ['required', 'string'];
            $rules['direct_deposit_info.branch_code'] = ['nullable', 'string'];
            $rules['direct_deposit_info.check_id'] = [
                'required',
                Rule::exists('attachments', 'id')->where(function ($query) {
                    $query->where('collection_name', CollectionName::Check);
                }),
            ];
        }

        // Conditionally add rules for pickup at office (extend for other methods as needed)
        if ($this->input('refund_method') === RefundMethod::PickupAtOffice->value) { // Adjust enum value if string literal
            $rules['pickup_info'] = ['required', 'array'];
            $rules['pickup_info.office_location'] = ['required', 'string', 'max:255'];
            $rules['pickup_info.appointment_date'] = ['required', 'date', 'after:now'];
        }

        return $rules;
    }
}
