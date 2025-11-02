<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'address' => $this->whenNotNull('address.name'),
            'apt' => $this->whenNotNull('address.apt'),
            'state' => $this->whenNotNull('address.state'),
            'date_of_birth' => $this->date_of_birth,
            'ssn' => $this->whenNotNull('legal.social_security_number'),
            'phone' => $this->phone,
            'zip_code' => $this->zip_code,
            'license_type' => $this->whenNotNull('identification.license_type'),
            'license_number' => $this->whenNotNull('identification.license_number'),
            'issuing_state' => $this->whenNotNull('identification.issuing_state'),
            'license_issue_date' => $this->whenNotNull('identification.license_issue_date'),
            'license_expiration_date' => $this->whenNotNull('identification.license_expiration_date'),
            'license_front_image_id' => $this->whenLoaded('identification', function () {
                return $this->identification->attachments()->where('metadata', 'license_front')->pluck('id');
            }),
            'license_back_image_id' => $this->whenLoaded('identification', function () {
                return $this->identification->attachments()->where('metadata', 'license_back')->pluck('id');
            }),
            'tax_station' => $this->whenNotNull('taxStation.name'),
            'legal_location' => $this->whenNotNull('legal.location.title'),
            'hear_from' => $this->hear_from,
            'filing_status' => $this->whenNotNull('legal.filing_status'),
            'occupation' => $this->occupation,
            'number_of_dependant' => $this->whenNotNull('legal.number_of_dependant'),
            'dependants' => DepandantResource::collection($this->whenLoaded('dependants')),
            'payment' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
