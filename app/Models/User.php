<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable;
    use InteractsWithMedia;
    use MediaTrait;
    use SoftDeletes;

    protected $with = ['media'];

    protected $appends = ['name', 'attachment'];

    protected $hidden = [
        'password',
        'updated_at',
        'deleted_at',
        'email_verified_at',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'role' => UserRole::class,
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('user-avatar')
            ->singleFile();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function profile()
    {
        return $this->hasMany(Profile::class, 'user_id');
    }

    // assigned to
    public function assignedTo()
    {
        return $this->hasMany(Profile::class, 'assigned_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOperation(): bool
    {
        return $this->role === UserRole::OPERATION;
    }

    public function isBranchManager(): bool
    {
        return $this->role === UserRole::BRANCH_MANAGER;
    }

    public function isAccountant(): bool
    {
        return $this->role === UserRole::ACCOUNTANT;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }
}
