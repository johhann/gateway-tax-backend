<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalResource extends JsonResource
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
            'profile_id' => $this->resource->profile_id,
            'legal_city' => new LegalCityResource($this->whenLoaded('legalCity')),
            'legal_location' => new LegalLocationResource($this->whenLoaded('legalLocation')),
            'social_security_number' => $this->resource->social_security_number,
            'filing_status' => $this->resource->filing_status,
            'spouse_information' => $this->resource->spouse_information,
            'dependants' => DepandantResource::collection($this->whenLoaded('profile.dependants')),
        ];
    }
}
