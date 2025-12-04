<?php

namespace App\Http\Controllers;

use App\Enums\ProfileUserStatus;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Jobs\ConvertAttachmentToPdf;
use App\Models\Profile;
use App\Notifications\FormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::query()
            ->withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->with([
                'address',
                'business',
                'legal',
                'dependants',
                'identification',
                'payment',
            ])->get();

        return ProfileResource::collection($profiles);
    }

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
    public function show(Request $request, $id)
    {
        $profile = Profile::query()->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return new ProfileResource($profile);
    }

    public function latest()
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

    public function submitProfile($id)
    {
        $profile = Profile::query()->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->user_status === ProfileUserStatus::SUBMITTED) {
            return response()->json(['message' => 'Profile already submitted'], 400);
        }

        $profile->user_status = ProfileUserStatus::SUBMITTED;
        $profile->save();

        ConvertAttachmentToPdf::dispatch($profile->id);

        $user = $profile->user;
        $user->notify(new FormSubmitted($profile));

        return new ProfileResource($profile);
    }
}
