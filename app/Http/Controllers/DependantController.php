<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDependantRequest;
use App\Http\Requests\UpdateDependantRequest;
use App\Models\Dependant;

class DependantController extends Controller
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
    public function store(StoreDependantRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dependant $dependant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDependantRequest $request, Dependant $dependant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dependant $dependant)
    {
        //
    }
}
