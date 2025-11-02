<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\LegalLocation;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legalLocations = LegalLocation::all();

        foreach ($legalLocations as $legalLocation) {
            Branch::create([
                'name' => fake()->city(),
                'legal_location_id' => $legalLocation->id,
            ]);
        }
    }
}
