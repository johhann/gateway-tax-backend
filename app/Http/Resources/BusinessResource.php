<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
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
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'address_line_one' => $this->resource->address_line_one,
            'address_line_two' => $this->resource->address_line_two,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'zip_code' => $this->resource->zip_code,
            'work_phone' => $this->resource->work_phone,
            'home_phone' => $this->resource->home_phone,
            'website' => $this->resource->website,
            'has_1099_misc' => $this->resource->has_1099_misc,
            'is_license_requirement' => $this->resource->is_license_requirement,
            'has_business_license' => $this->resource->has_business_license,
            'advertise_through' => $this->resource->advertise_through,
            'business_advertisement' => $this->resource->business_advertisement,
            'records' => $this->resource->records,
            'other_record' => $this->resource->other_record,
            'file_taxed_for_tax_year' => $this->resource->file_taxed_for_tax_year,
        ];
    }
}
