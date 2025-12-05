<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Services\TaxPassFtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadPdfToFtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $attachmentId) {}

    public function handle(TaxPassFtpService $ftpService): void
    {
        $attachment = Attachment::find($this->attachmentId);

        if (! $attachment) {
            return;
        }

        $media = $attachment->getFirstMedia($attachment->collection_name->value);

        if (! $media) {
            return;
        }

        if (! file_exists($media->getPath())) {
            return;
        }

        $content = file_get_contents($media->getPath());
        $ftpService->upload($content, $media->file_name);
    }
}
