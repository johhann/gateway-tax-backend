<?php

namespace App\Http\Controllers;

use App\Enums\CollectionName;
use App\Http\Requests\StoreIdentificationRequest;
use App\Http\Requests\UpdateIdentificationRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\IdentificationResource;
use App\Models\Address;
use App\Models\Identification;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class IdentificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(StoreIdentificationRequest $request)
    {
        $validated = $request->validated();

        $profile = Profile::with(['identification', 'address'])->find($validated['profile_id']);

        abort_if($profile->identification, 409, 'Identification already exists.');
        abort_if($profile->address, 409, 'Address already exists.');

        DB::beginTransaction();

        $identification = Identification::updateOrCreate([
            'profile_id' => $validated['profile_id'],
            'zip_code' => $validated['zip_code'] ?? null,
            'license_type' => $validated['license_type'],
            'license_number' => $validated['license_number'],
            'issuing_state' => $validated['issuing_state'],
            'license_issue_date' => $validated['license_issue_date'],
            'license_expiration_date' => $validated['license_expiration_date'],
        ]);

        $attachments = [$validated['license_front_image_id'], $validated['license_back_image_id']];
        $identification->attachAttachments($attachments);

        $address = Address::updateOrCreate([
            'profile_id' => $validated['profile_id'],
            'address' => $validated['address'],
            'apt' => $validated['apt'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'zip_code' => $validated['zip_code'] ?? $profile->zip_code,
        ]);

        DB::commit();

        return response()->json([
            'identification' => new IdentificationResource($identification),
            'address' => new AddressResource($address),
            'license_front_image_id' => $identification->attachments()->where('collection_name', CollectionName::IdFront)->latest()->pluck('id'),
            'license_back_image_id' => $identification->attachments()->where('collection_name', CollectionName::IdBack)->latest()->pluck('id'),
        ], 201);
    }

    /**
     * Display the authenticated user's identification and address.
     */
    public function show(Request $request, $id)
    {
        $profile = Profile::where('id', $id)->with([
            'identification', 'address',
        ])->first() ?? null;

        if (is_null($profile)) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $identification = $profile->identification;
        $address = $profile->address;

        return response()->json([
            'identification' => $identification ? new IdentificationResource($identification) : null,
            'address' => $address ? new AddressResource($address) : null,
            'license_front_image_id' => $identification ? $identification->attachments()->where('collection_name', CollectionName::IdFront)->latest()->pluck('id') : [],
            'license_back_image_id' => $identification ? $identification->attachments()->where('collection_name', CollectionName::IdBack)->latest()->pluck('id') : [],
        ]);
    }

    /**
     * Update the authenticated user's identification and address.
     *
     * @throws Throwable
     */
    public function update(UpdateIdentificationRequest $request)
    {
        $validated = $request->validated();

        $profile = Profile::where('id', $validated['profile_id'])->with([
            'identification',
            'address',
        ])->first() ?? null;

        if (is_null($profile)) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $identification = $profile->identification;
        if (! $identification) {
            return response()->json(['message' => 'Identification not found.'], 404);
        }

        $address = $profile->address;

        DB::transaction(function () use ($validated, $identification, &$address, $profile) {
            $identData = [];
            foreach (['license_type', 'license_number', 'issuing_state', 'license_issue_date', 'license_expiration_date'] as $key) {
                if (array_key_exists($key, $validated)) {
                    $identData[$key] = $validated[$key];
                }
            }
            if (! empty($identData)) {
                $identification->update($identData);
            }

            $addrData = [];
            foreach (['address', 'apt', 'city', 'state'] as $key) {
                if (array_key_exists($key, $validated)) {
                    $addrData[$key] = $validated[$key];
                }
            }

            if (! empty($addrData)) {
                $addrData['zip_code'] = $profile->zip_code;
                if ($address) {
                    $address->update($addrData);
                } else {
                    $addrData['profile_id'] = $profile->id;
                    $address = Address::create($addrData);
                }
            }

            if (array_key_exists('license_front_image_id', $validated)) {
                $frontAttachments = $identification->attachments()->where('collection_name', CollectionName::IdFront)->pluck('id')->toArray();
                $identification->detachAttachments($frontAttachments);
                $identification->attachAttachments($validated['license_front_image_id']);
            }

            if (array_key_exists('license_back_image_id', $validated)) {
                $backAttachments = $identification->attachments()->where('collection_name', CollectionName::IdBack)->pluck('id')->toArray();
                $identification->detachAttachments($backAttachments);
                $identification->attachAttachments($validated['license_back_image_id']);
            }
        });

        $identification->refresh();
        $address = $address ? $address->fresh() : null;

        return response()->json([
            'identification' => new IdentificationResource($identification->refresh()),
            'address' => $address ? new AddressResource($address->refresh()) : null,
            'license_front_image_id' => $identification->attachments()->where('collection_name', CollectionName::IdFront)->latest()->pluck('id'),
            'license_back_image_id' => $identification->attachments()->where('collection_name', CollectionName::IdBack)->latest()->pluck('id'),
        ]);
    }
}
