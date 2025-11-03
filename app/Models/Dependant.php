<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dependant';

    public function legal(): BelongsTo
    {
        return $this->belongsTo(Legal::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
