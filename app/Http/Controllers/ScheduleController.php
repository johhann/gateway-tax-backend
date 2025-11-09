<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $profile = auth()->user()->profile;
        $schedule = Schedule::query()->updateOrCreate(
            [
                'profile_id' => $profile->id,
                'scheduled_start_time' => $request->input('scheduled_start_time'),
                'scheduled_end_time' => $request->input('scheduled_end_time'),
            ],
            $request->validated()
        );

        return (new ScheduleResource($schedule))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $schedule = Schedule::query()->where('profile_id', auth()->user()->profile->id)->latest()->get();

        return new ScheduleResource($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request)
    {
        $profile = auth()->user()->profile;
        $schedule = Schedule::query()->where('profile_id', $profile->id)->first();

        if (! $schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $schedule->update($request->validated());

        return new ScheduleResource($schedule->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
