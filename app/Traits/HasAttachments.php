<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

trait HasAttachments
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'record');
    }

    public function attachAttachments(array|int $attachmentIds): void
    {
        $ids = is_array($attachmentIds) ? $attachmentIds : [$attachmentIds];

        $attachments = Attachment::query()->whereIn('id', $ids)->get();

        if ($attachments->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'attachments' => 'Some attachments do not exist.',
            ]);
        }

        $alreadyLinked = $attachments->filter(function (Attachment $attachment) {
            return $attachment->isAttached();
        });

        if ($alreadyLinked->isNotEmpty()) {
            throw ValidationException::withMessages([
                'attachments' => 'Some attachments are already attached to another record.',
            ]);
        }

        $attachments->each(function (Attachment $attachment) {
            $attachment->record()->associate($this);
            $attachment->save();
        });
    }
}
