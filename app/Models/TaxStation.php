<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxStation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
