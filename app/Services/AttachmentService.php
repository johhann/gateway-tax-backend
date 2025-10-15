<?php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachmentService
{
    public static function delete(Media $media)
    {
        $media->delete();

        return 'Attachment deleted successfully';
    }
}
