<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'w2_id' => $this->resource->w2_id,
            'misc_1099_id' => $this->resource->misc_1099_id,
            'mortgage_statement_id' => $this->resource->mortgage_statement_id,
            'tuition_statement_id' => $this->resource->tuition_statement_id,
            'shared_riders_id' => $this->resource->shared_riders_id,
            'misc_id' => $this->resource->misc_id,
        ];
    }
}
