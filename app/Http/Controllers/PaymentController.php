<?php

namespace App\Http\Controllers;

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
        $checkAttachment = $data['direct_deposit_info.check_id'];
        unset($data['direct_deposit_info.check_id']);

        $payment = Payment::query()->updateOrCreate(
            ['profile_id' => auth()->user()->profile->id],
            $data
        );
        $payment->attachAttachments($checkAttachment);

        return (new PaymentResource($payment))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $payment = Payment::where('profile_id', auth()->user()->profile->id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return new PaymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment = Payment::where('profile_id', auth()->user()->profile->id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $data = $request->validated();
        $checkAttachment = $data['direct_deposit_info.check_id'] ?? null;
        unset($data['direct_deposit_info.check_id']);

        $payment->fill($data);
        if ($checkAttachment) {
            $payment->attachAttachments($checkAttachment);
        }

        $payment->save();

        return new PaymentResource($payment);
    }
}
