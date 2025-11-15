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
            'id' => $this->id,
            'year' => $this->tax_year,
            'full_name' => $this->full_name,
            'ssn' => $this->ssn,
            'specific_request' => $this->specific_request,
            'status' => $this->status->value,
        ];
    }
}
