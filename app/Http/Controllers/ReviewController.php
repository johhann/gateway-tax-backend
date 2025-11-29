<?php

namespace App\Http\Controllers;

use App\Enums\CollectionName;
use App\Models\Profile;
use App\Models\Scopes\ProfileScope;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $id)
    {
        $profile = Profile::withoutGlobalScope(ProfileScope::class)->get()->where('id', $id)
            ->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $attachments = $profile->attachments()
            ->where('collection_name', CollectionName::PDFAttachments->value)
            ->get();

        $pdfs = [];

        foreach ($attachments as $attachment) {
            $medias = $attachment->getMedia(CollectionName::PDFAttachments->value);
            foreach ($medias as $media) {
                $pdfs[] = [
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                ];
            }
        }

        return response()->json(['pdfs' => $pdfs]);
    }
}
