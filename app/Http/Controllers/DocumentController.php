<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Profile;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        $profile = Profile::find($data['profile_id']);

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (Document::query()->where('profile_id', $profile->id)->exists()) {
            return response()->json(['message' => 'Document already exists for profile'], 409);
        }

        $doc = Document::query()->create($data);

        $attachmentFields = [
            'w2_id',
            'misc_1099_id',
            'mortgage_statement_id',
            'tuition_statement_id',
            'shared_riders_id',
            'misc_id',
        ];

        foreach ($attachmentFields as $field) {
            if (! empty($data[$field])) {
                $attachment = Attachment::find($data[$field]);

                if (! $attachment) {
                    continue;
                }

                if ($attachment->isAttached() && ! ($attachment->record_type === Profile::class && $attachment->record_id === $profile->id)) {
                    throw ValidationException::withMessages([
                        $field => 'Attachment is already attached to another record.',
                    ]);
                }

                $attachment->record()->associate($profile);
                $attachment->save();
            }
        }

        return new DocumentResource($doc);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $profile = Profile::find($id);

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $document = Document::query()->where('profile_id', $profile->id)->first();

        if (! $document) {
            return response()->json(['message' => 'Document not found for profile'], 404);
        }

        return new DocumentResource($document);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $data = $request->validated();

        $profile = Profile::where('id', $document->profile_id)->first();

        if ($profile->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $attachmentFields = [
            'w2_id',
            'misc_1099_id',
            'mortgage_statement_id',
            'tuition_statement_id',
            'shared_riders_id',
            'misc_id',
        ];

        foreach ($attachmentFields as $field) {
            if (array_key_exists($field, $data)) {
                $newId = $data[$field];
                $oldId = $document->{$field};

                if ($newId === $oldId) {
                    continue;
                }

                if ($oldId) {
                    $oldAttachment = Attachment::find($oldId);
                    if ($oldAttachment && $oldAttachment->record_type === Profile::class && $oldAttachment->record_id === $profile->id) {
                        $oldAttachment->record()->dissociate();
                        $oldAttachment->save();
                    }
                }

                if ($newId) {
                    $newAttachment = Attachment::findOrFail($newId);

                    if ($newAttachment->isAttached() && ! ($newAttachment->record_type === Profile::class && $newAttachment->record_id === $profile->id)) {
                        throw ValidationException::withMessages([
                            $field => 'Attachment is already attached to another record.',
                        ]);
                    }

                    $newAttachment->record()->associate($profile);
                    $newAttachment->save();
                }

                $document->{$field} = $newId;
            }
        }

        $document->save();

        return new DocumentResource($document);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        //
    }
}
