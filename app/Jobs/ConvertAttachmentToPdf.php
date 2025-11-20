<?php

namespace App\Jobs;

use App\Enums\CollectionName;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Identification;
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

    public function __construct(public int $profileId) {}

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws Exception
     */
    public function handle(ImageToPdfService $service): void
    {
        $attachments = collect();

        $document = Document::where('profile_id', $this->profileId)->first();
        if ($document) {
            foreach (['w2_id', 'misc_1099_id', 'shared_riders_id', 'mortgage_statement_id', 'tuition_statement_id', 'misc_id'] as $field) {
                $id = $document->{$field};
                if ($id) {
                    $att = Attachment::find($id);
                    if ($att) {
                        $attachments->push($att);
                    }
                }
            }
        }

        $identification = Identification::where('profile_id', $this->profileId)->first();
        if ($identification) {
            $identification->load('attachments');
            $identification->attachments->each(function (Attachment $a) use ($attachments) {
                $attachments->push($a);
            });
        }

        $attachments = $attachments->unique('id')->values();

        foreach ($attachments as $attachment) {
            $medias = $attachment->getMedia();
            foreach ($medias as $media) {
                $sourcePath = $media->getPath();
                if (! $sourcePath || ! is_file($sourcePath)) {
                    continue;
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
    }
}
