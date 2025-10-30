<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function business(): HasOne
    {
        return $this->hasOne(Business::class)->latestOfMany();
    }

    public function legal(): HasOne
    {
        return $this->hasOne(Legal::class)->latestOfMany();
    }

    public function dependant(): HasMany
    {
        return $this->hasMany(Dependant::class);
    }

    public function taxStation(): BelongsTo
    {
        return $this->belongsTo(TaxStation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identification(): HasOne
    {
        return $this->hasOne(Identification::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
