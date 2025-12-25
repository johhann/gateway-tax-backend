<?php

namespace App\Http\Controllers;

use App\Enums\RefundMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $checkAttachment = null;
        $additionalData = [];

        // Conditionally process direct deposit info only if refund_method indicates direct deposit
        if (isset($data['refund_method']) && $data['refund_method'] === RefundMethod::DirectDeposit->value) {
            if (! isset($data['direct_deposit_info']['check_id'])) {
                return response()->json(['data' => ['error' => 'Check ID is required for direct deposit.']], 422);
            }
            $checkAttachment = $data['direct_deposit_info']['check_id'];
            unset($data['direct_deposit_info']['check_id']);
            $additionalData = $data['direct_deposit_info'];
            unset($data['direct_deposit_info']);
        } elseif (isset($data['refund_method']) && $data['refund_method'] === RefundMethod::PickupAtOffice->value) {
            $additionalData = $data['pickup_info'] ?? [];
            if (!empty($data['pickup_info'])) unset($data['pickup_info']);
        }

        $data = array_merge($data, ['data' => $additionalData]);

        $payment = Payment::query()->updateOrCreate(
            ['profile_id' => $data['profile_id']],
            $data
        );

        // Attach attachment only if it exists (i.e., for direct deposit)
        if ($checkAttachment) {
            $payment->attachAttachments($checkAttachment);
        }

        return (new PaymentResource($payment->load(['attachment'])))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = Payment::where('profile_id', $id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return new PaymentResource($payment->load(['attachment']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment = Payment::where('profile_id', $request->profile_id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $data = $request->validated();
        $additionalData = [];

        // Conditionally process direct deposit info only if refund_method indicates direct deposit
        if (isset($data['refund_method']) && $data['refund_method'] === RefundMethod::DirectDeposit->value) {
            $checkAttachment = $data['direct_deposit_info']['check_id'] ?? null;
            if ($checkAttachment) {
                unset($data['direct_deposit_info']['check_id']);
            }
            $additionalData = $data['direct_deposit_info'];
            unset($data['direct_deposit_info']);
        } elseif (isset($data['refund_method']) && $data['refund_method'] === RefundMethod::PickupAtOffice->value) {
            $additionalData = $data['pickup_info'];
            unset($data['pickup_info']);
        }

        $data = array_merge($data, ['data' => $additionalData]);

        $payment->fill($data);
        if ($checkAttachment) {
            $prevAttachments = $payment->attachments()->pluck('id')->toArray();
            $payment->detachAttachments($prevAttachments);
            $payment->attachAttachments($checkAttachment);
        }

        $payment->save();

        $payment->refresh();

        return new PaymentResource($payment->load(['attachment']));
    }
}
