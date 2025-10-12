<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legal extends Model
{
    /** @use HasFactory<\Database\Factories\LegalFactory> */
    use HasFactory;

    use SoftDeletes;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(LegalCity::class);
    }
}
