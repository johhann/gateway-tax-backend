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
        return [
            'type' => ['required_without_all:refund_method,refund_fee', 'string', Rule::in(RefundType::values())],
            'refund_method' => ['required_without_all:type,refund_fee', 'string', Rule::in(RefundMethod::values())],

            'direct_deposit_info' => [
                Rule::requiredIf(
                    fn () => $this->input('refund_method') === RefundMethod::DirectDeposit->value
                    // ||
                    // $this->input('refund_fee') === RefundFee::DirectDeposit->value
                ),
                'array',
            ],

            'direct_deposit_info.bank_name' => ['required_with:direct_deposit_info', 'string'],
            'direct_deposit_info.account_type' => ['required_with:direct_deposit_info', 'string', Rule::in(DirectDepositAccountType::values())],
            'direct_deposit_info.account_holder' => ['required_with:direct_deposit_info', 'string'],
            'direct_deposit_info.routing_number' => ['required_with:direct_deposit_info', 'string'],
            'direct_deposit_info.account_number' => ['required_with:direct_deposit_info', 'string'],
            'direct_deposit_info.branch_code' => ['nullable', 'string'],
            'direct_deposit_info.check_id' => ['required_with:direct_deposit_info', Rule::exists('attachments', 'id')->where(function ($query) {
                $query->where('collection_name', CollectionName::Check);
            })],

            'pickup_info' => [
                Rule::requiredIf(
                    fn () => $this->input('refund_method') === RefundMethod::PickupAtOffice->value
                ),
                'array',
            ],

            'pickup_info.branch_id' => ['required_with:pickup_info', 'exists:branches,id'],
            'pickup_info.appointment_date' => ['required_with:pickup_info', 'date_format:Y-m-d'],

            'refund_fee' => ['required_without_all:type,refund_method', 'string', Rule::in(RefundFee::values())],
            'profile_id' => ['required', 'exists:profiles,id'],
        ];
    }
}
