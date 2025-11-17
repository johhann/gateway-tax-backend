<?php

namespace App\Http\Controllers;

use App\Enums\ProfileUserStatus;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['user_status'] = ProfileUserStatus::DRAFT;

        $profile = Profile::query()->create($data);

        return (new ProfileResource($profile))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $profile = Profile::query()->where('user_id', Auth::id())
            ->latest()->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return new ProfileResource($profile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request)
    {
        $profile = Profile::query()->where('user_id', Auth::id())
            ->latest()->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->update($request->validated());

        return new ProfileResource($profile);
    }
}
