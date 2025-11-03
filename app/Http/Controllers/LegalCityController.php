<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalCityRequest;
use App\Http\Requests\UpdateLegalCityRequest;
use App\Http\Resources\LegalCityResource;
use App\Models\LegalCity;

class LegalCityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        return LegalCityResource::collection(LegalCity::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegalCityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LegalCity $legalCity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalCityRequest $request, LegalCity $legalCity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalCity $legalCity)
    {
        //
    }
}
