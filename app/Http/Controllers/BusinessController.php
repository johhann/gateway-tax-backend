<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Models\Business;

class BusinessController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessRequest $request)
    {
        $data = $request->validated();
        $business = Business::query()->updateOrCreate(
            ['profile_id' => auth()->user()->profiles->latest()->first()->id],
            $data
        );

        return (new BusinessResource($business))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $business = Business::where('profile_id', auth()->user()->profiles->latest()->first()->id)->first();

        if (! $business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        return new BusinessResource($business);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRequest $request)
    {
        $business = Business::where('profile_id', auth()->user()->profiles->id)->latest()->first();

        if (! $business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $business->update($request->validated())->refresh();

        return new BusinessResource($business);
    }
}
