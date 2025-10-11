<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalRequest;
use App\Http\Requests\UpdateLegalRequest;
use App\Models\Legal;

class LegalController extends Controller
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
    public function store(StoreLegalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Legal $legal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalRequest $request, Legal $legal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Legal $legal)
    {
        //
    }
}
