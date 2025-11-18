<?php

namespace App\Models;

use App\Enums\ProfileProgressStatus;
use App\Enums\ProfileUserStatus;
use App\Models\Scopes\ProfileScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(ProfileScope::class)]
class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['name'];

    protected $casts = [
        'progress_status' => ProfileProgressStatus::class,
        'user_status' => ProfileUserStatus::class,
        'date_of_birth' => 'datetime',
    ];

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

    public function dependants(): HasMany
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'assigned_branch_id');
    }

    public function taxRequests(): HasMany
    {
        return $this->hasMany(TaxRequest::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    protected function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->middle_name.' '.$this->last_name;
    }
}
