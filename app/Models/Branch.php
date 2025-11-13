<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'assigned_branch_id');
    }

    public function legalCity(): BelongsTo
    {
        return $this->belongsTo(LegalCity::class);
    }
}
