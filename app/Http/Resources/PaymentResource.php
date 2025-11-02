<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'refund_method' => $this->refund_method,
            'direct_deposit_info' => $this->direct_deposit_info,
            'refund_fee' => $this->refund_fee,
            'check_id' => $this->whenLoaded('attachment', 'id'),
        ];
    }
}
