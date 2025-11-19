<?php

namespace App\Jobs;

use App\Enums\CollectionName;
use App\Models\Attachment;
use App\Services\ImageToPdfService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ConvertAttachmentToPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $attachmentId, public int $mediaId) {}

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws Exception
     */
    public function handle(ImageToPdfService $service): void
    {
        $attachment = Attachment::query()->find($this->attachmentId);
        if (! $attachment) {
            return;
        }

        $media = $attachment->media()->where('id', $this->mediaId)->first();
        if (! $media) {
            return;
        }

        $sourcePath = $media->getPath();
        if (! $sourcePath || ! is_file($sourcePath)) {
            return;
        }

        $baseName = pathinfo($media->file_name, PATHINFO_FILENAME);

        $tmpBase = tempnam(sys_get_temp_dir(), 'pdf_');
        @unlink($tmpBase);
        $tmpPdf = $tmpBase.'.pdf';

        $service->convertToPdf($sourcePath, $tmpPdf, 3 * 1024 * 1024);

        if (is_file($tmpPdf)) {
            $attachment
                ->addMedia($tmpPdf)
                ->usingFileName($baseName.'.pdf')
                ->toMediaCollection(CollectionName::PDFAttachments->value);

            @unlink($tmpPdf);
        }
    }
}
