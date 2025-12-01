<?php

namespace App\Models;

use App\Enums\TaxRequestStatus;
use App\Models\Scopes\TaxRequestScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ScopedBy(TaxRequestScope::class)]
class TaxRequest extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'full_name' => 'string',
            'ssn' => 'string',
            'specific_request' => 'string',
            'status' => TaxRequestStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
