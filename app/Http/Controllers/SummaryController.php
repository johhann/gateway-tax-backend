<?php

namespace App\Http\Controllers;

use App\Http\Resources\SummaryResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return new SummaryResource(Profile::query()->where('id', auth()->user()->profile->id)->with(['legal', 'legal.location', 'dependants', 'payment', 'taxStation', 'identification', 'address'])->firstOrFail());
    }
}
