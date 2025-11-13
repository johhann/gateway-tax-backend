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
        DB::transaction(function () use ($validated, $dependants, &$legal) {
            $profile = auth()->user()->profile;
            $legal = Legal::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                $validated
            );

            foreach ($dependants as $dependantData) {
                $profile->dependants()->create($dependantData);
            }
        });

        return (new LegalResource($legal->load(['city', 'branch', 'profile.dependants'])))->response()->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $legal = Legal::query()->where('legals.profile_id', auth()->user()->profile->id)
            ->with(['legalCity', 'legalLocation', 'dependants'])
            ->firstOrFail();

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

        DB::transaction(function () use ($dependants, $validated) {
            $legal = Legal::query()->where('legals.profile_id', auth()->user()->profile->id)
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
        });

        $updatedLegal = Legal::query()->where('legals.profile_id', auth()->user()->profile->id)
            ->with(['legalCity', 'legalLocation', 'dependants'])
            ->firstOrFail();

        return new LegalResource($updatedLegal);
    }
}
