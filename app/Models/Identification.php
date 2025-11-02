<?php

namespace App\Models;

use App\Enums\LicenseType;
use App\Enums\StateEnum;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Identification extends Model
{
    use HasAttachments;
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'license_type' => LicenseType::class,
        'issuing_state' => StateEnum::class,
        'license_issue_date' => 'date',
        'license_expiration_date' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
