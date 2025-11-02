<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaxStationResource;
use App\Models\TaxStation;

class TaxStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        return TaxStationResource::collection(TaxStation::all());
    }
}
