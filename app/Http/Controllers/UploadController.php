<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Throwable;

class UploadController extends Controller
{
    public function show(Attachment $attachment)
    {
        if ($attachment->user_id !== Auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'You do not have permission to view this attachment.',
            ], 403);
        }

        $media = $attachment->getFirstMedia(
            $attachment->collection_name
        );
        if (! $media) {
            return response()->json([
                'message' => 'No media found for this attachment.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attachment retrieved successfully.',
            'data' => [
                'id' => $attachment->id,
                'file_name' => $media->file_name,
                'collection_name' => $attachment->collection_name,
                'url' => $media->getFullUrl(),
                'metadata' => $attachment->metadata,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(StoreAttachmentRequest $request)
    {
        $data = $request->validated();
        $collectionName = $data['collection_name'];
        $file = $request->file('file');
        $metadata = $data['metadata'];

        DB::beginTransaction();
        try {
            $attachment = new Attachment;
            $attachment->user_id = Auth()->id();
            $attachment->metadata = $metadata;
            $attachment->collection_name = $collectionName;
            $attachment->save();
            $media = $attachment->addMedia($file)
                ->toMediaCollection($collectionName);
            DB::commit();

            return response()->json([
                'message' => 'File uploaded successfully.',
                'data' => [
                    'id' => $attachment->id,
                    'file_name' => $media->file_name,
                    'collection_name' => $collectionName,
                    'url' => $media->getFullUrl(),
                    'metadata' => $attachment->metadata,
                ],
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to upload file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        if ($attachment->user_id !== Auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'You do not have permission to delete this attachment.',
            ], 403);
        }

        try {
            $attachment->delete();

            return response()->json([
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete attachment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
