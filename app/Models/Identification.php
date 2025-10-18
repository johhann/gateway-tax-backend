<?php

namespace App\Models;

use App\Enums\LicenseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Identification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'id_type' => LicenseType::class,
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
