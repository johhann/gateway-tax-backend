<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalRequest;
use App\Http\Requests\UpdateLegalRequest;
use App\Http\Resources\LegalResource;
use App\Models\Legal;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class LegalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegalRequest $request)
    {
        $validated = $request->validated();

        $dependants = $validated['dependants'] ?? [];
        unset($validated['dependants']);

        $legal = null;
        DB::beginTransaction();
        $legal = Legal::query()->updateOrCreate(
            ['profile_id' => $validated['profile_id']],
            $validated
        );

        foreach ($dependants as $dependantData) {
            $legal->profile->dependants()->create($dependantData);
        }

        DB::commit();

        return (new LegalResource($legal->load(['city', 'branch', 'profile.dependants'])))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $legal = Legal::query()->where('legals.profile_id', $id)
            ->with(['city', 'branch'])
            ->first();

        if (! $legal) {
            return response()->json(['message' => 'Legal not found.'], 404);
        }

        $legal->dependants = $legal->profile->dependants;

        return new LegalResource($legal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalRequest $request)
    {
        $validated = $request->validated();
        $profile = Profile::query()->where('id', $validated['profile_id'])->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $dependants = $validated['dependants'] ?? [];
        unset($validated['dependants']);

        DB::beginTransaction();
        $legal = Legal::query()->where('legals.profile_id', $validated['profile_id'])
            ->firstOrFail();

        $legal->update($validated);

        $existingDependantIds = $profile->dependants->pluck('id')->toArray();
        $incomingDependantIds = collect($dependants)->pluck('id')->filter()->toArray();

        $dependantsToDelete = array_diff($existingDependantIds, $incomingDependantIds);

        if (! empty($dependantsToDelete)) {
            $profile->dependants()->whereIn('id', $dependantsToDelete)->delete();
        }

        foreach ($dependants as $dependantData) {
            if (isset($dependantData['id'])) {
                $profile->dependants()->where('id', $dependantData['id'])->update($dependantData);
            } else {
                $profile->dependants()->create($dependantData);
            }
        }

        DB::commit();

        $updatedLegal = $legal
            ->loadMissing(['city', 'branch', 'profile.dependants'])
            ->firstOrFail();

        return new LegalResource($updatedLegal);
    }
}
