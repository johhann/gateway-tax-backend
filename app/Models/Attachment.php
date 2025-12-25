<?php

namespace App\Models;

use App\Enums\CollectionName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Attachment extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $with = ['media'];

    protected function casts(): array
    {
        return [
            'collection_name' => CollectionName::class,
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }

    public function isAttached(): bool
    {
        return $this->record_type !== null && $this->record_id !== null;
    }
}
