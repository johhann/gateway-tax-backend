<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IdentificationResource extends JsonResource
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
            'zip_code' => $this->resource->zip_code,
            'license_type' => $this->resource->license_type,
            'license_number' => $this->resource->license_number,
            'issuing_state' => $this->resource->issuing_state,
            'license_issue_date' => $this->resource->license_issue_date,
            'license_expiration_date' => $this->resource->license_expiration_date,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
