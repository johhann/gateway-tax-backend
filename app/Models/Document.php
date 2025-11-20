<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'profile_id',
        'w2_id',
        'misc_1099_id',
        'mortgage_statement_id',
        'tuition_statement_id',
        'shared_riders_id',
        'misc_id',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function w2(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'w2_id');
    }

    public function misc1099(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'misc_1099_id');
    }

    public function mortgageStatement(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'mortgage_statement_id');
    }

    public function tuitionStatement(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'tuition_statement_id');
    }

    public function sharedRiders(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'shared_riders_id');
    }

    public function misc(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'misc_id');
    }
}
