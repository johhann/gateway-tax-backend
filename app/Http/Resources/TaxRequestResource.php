<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRequestResource extends JsonResource
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
            'year' => $this->resource->year,
            'full_name' => $this->resource->full_name,
            'ssn' => $this->resource->ssn,
            'specific_request' => $this->resource->specific_request,
        ];
    }
}
