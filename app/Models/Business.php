<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'advertise_through' => 'json',
        'records' => 'json',
        'has_1099_misc' => 'boolean',
        'is_license_requirement' => 'boolean',
        'has_business_license' => 'boolean',
        'file_taxed_for_file_year' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
