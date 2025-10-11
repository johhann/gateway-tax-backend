<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaxStationRequest;
use App\Http\Requests\UpdateTaxStationRequest;
use App\Models\TaxStation;

class TaxStationController extends Controller
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
    public function store(StoreTaxStationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxStation $taxStation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxStationRequest $request, TaxStation $taxStation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxStation $taxStation)
    {
        //
    }
}
