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
            //            'taxes_last_year' => $this->resource->taxes_last_year,
            'hear_from' => $this->resource->hear_from,
            'occupation' => $this->resource->occupation,
            'self_employment_income' => (bool) $this->resource->self_employment_income,
        ];
    }
}
