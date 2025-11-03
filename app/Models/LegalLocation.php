<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalLocation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function city(): BelongsTo
    {
        return $this->belongsTo(LegalCity::class, 'legal_city_id');
    }

    public function legals(): HasMany
    {
        return $this->hasMany(Legal::class);
    }
}
