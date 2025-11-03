<?php

namespace App\Http\Controllers;

use App\Http\Resources\LegalLocationResource;
use App\Models\LegalLocation;

class LegalLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(int $legalCityId)
    {
        $legalLocation = LegalLocation::query()->where('legal_city_id', $legalCityId)->get();

        return LegalLocationResource::collection($legalLocation);
    }
}
