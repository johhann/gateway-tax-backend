<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'percent' => $this->resource['percent'] ?? 0,
            'last_saved_step' => $this->resource['last_saved_step'] ?? null,
        ];
    }
}
