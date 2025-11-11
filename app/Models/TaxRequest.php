<?php

namespace App\Models;

use App\Enums\TaxRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRequest extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'full_name' => 'string',
            'ssn' => 'string',
            'specific_request' => 'string',
            'status' => TaxRequestStatus::class,
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
