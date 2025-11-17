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
    public function __invoke(Request $request, $id)
    {
        return new SummaryResource(Profile::query()->where('id', $id)->with(['legal', 'legal.branch', 'dependants', 'payment', 'taxStation', 'identification', 'address'])->firstOrFail());
    }
}
