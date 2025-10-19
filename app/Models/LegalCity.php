<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalCity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'locations' => 'array',
    ];

    public function legals(): HasMany
    {
        return $this->hasMany(Legal::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(LegalLocation::class);
    }
}
