<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaxRequestRequest;
use App\Http\Requests\UpdateTaxRequestRequest;
use App\Http\Resources\TaxStationResource;
use App\Models\TaxRequest;

class TaxRequestController extends Controller
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
    public function store(StoreTaxRequestRequest $request)
    {
        $profile = $request->user()->profile;
        $taxRequest = new TaxRequest($request->validated());
        $taxRequest->profile()->associate($profile);
        $taxRequest->save();

        return (new TaxStationResource($taxRequest))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxRequest $taxRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxRequestRequest $request, TaxRequest $taxRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxRequest $taxRequest)
    {
        //
    }
}
