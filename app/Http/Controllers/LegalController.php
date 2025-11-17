<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalRequest;
use App\Http\Requests\UpdateLegalRequest;
use App\Http\Resources\LegalResource;
use App\Models\Legal;
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
            ->with(['legalCity', 'branches', 'dependants'])
            ->first();

        if (! $legal) {
            return response()->json(['message' => 'Legal not found.'], 404);
        }

        return new LegalResource($legal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalRequest $request)
    {
        $validated = $request->validated();
        $dependants = $validated['dependants'] ?? [];
        unset($validated['dependants']);

        DB::beginTransaction();
        $legal = Legal::query()->where('legals.profile_id', $validated['profile_id'])
            ->with(['dependants'])
            ->firstOrFail();

        $legal->update($validated);

        $existingDependantIds = $legal->dependants->pluck('id')->toArray();
        $incomingDependantIds = collect($dependants)->pluck('id')->filter()->toArray();

        $dependantsToDelete = array_diff($existingDependantIds, $incomingDependantIds);

        if (! empty($dependantsToDelete)) {
            $legal->dependants()->whereIn('id', $dependantsToDelete)->delete();
        }

        foreach ($dependants as $dependantData) {
            if (isset($dependantData['id'])) {
                $legal->dependants()->where('id', $dependantData['id'])->update($dependantData);
            } else {
                $legal->dependants()->create($dependantData);
            }
        }

        DB::commit();

        $updatedLegal = $legal
            ->loadMissing(['legalCity', 'legalLocation', 'dependants'])
            ->firstOrFail();

        return new LegalResource($updatedLegal);
    }
}
