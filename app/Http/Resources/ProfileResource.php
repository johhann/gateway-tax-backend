<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'middle_name' => $this->resource->middle_name,
            'last_name' => $this->resource->last_name,
            'date_of_birth' => $this->resource->date_of_birth->toDateString(),
            'phone' => $this->resource->phone,
            'zip_code' => $this->resource->zip_code,
            'tax_station_id' => $this->resource->tax_station_id,
            'hear_from' => $this->resource->hear_from,
            'occupation' => $this->resource->occupation,
            'self_employment_income' => (bool) $this->resource->self_employment_income,
            'address' => new AddressResource($this->whenLoaded('address')),
            'business' => new BusinessResource($this->whenLoaded('business')),
            'legal' => new LegalResource($this->whenLoaded('legal')),
            'dependants' => DepandantResource::collection($this->whenLoaded('dependants')),
            'identification' => new IdentificationResource($this->whenLoaded('identification')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
        ];
    }
}
