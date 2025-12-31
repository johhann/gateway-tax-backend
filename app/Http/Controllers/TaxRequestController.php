<?php

namespace App\Http\Controllers;

use App\Enums\TaxRequestStatus;
use App\Http\Requests\StoreTaxRequestRequest;
use App\Http\Requests\UpdateTaxRequestRequest;
use App\Http\Resources\TaxRequestResource;
use App\Models\TaxRequest;
use Illuminate\Support\Facades\Auth;

class TaxRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxRequests = TaxRequest::where('user_id', Auth::id())->latest()->get();

        return TaxRequestResource::collection($taxRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxRequestRequest $request)
    {
        $taxRequest = new TaxRequest(array_merge($request->validated(), [
            'status' => TaxRequestStatus::Pending,
            'user_id' => Auth::id(),
        ]));
        $taxRequest->save();

        return (new TaxRequestResource($taxRequest))->response()->setStatusCode(201);
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
