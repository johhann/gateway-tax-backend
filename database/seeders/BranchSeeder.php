<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\LegalCity;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legalCities = LegalCity::all();

        foreach ($legalCities as $legalCity) {
            Branch::create([
                'name' => fake()->city(),
                'legal_location_id' => $legalCity->id,
            ]);
        }
    }
}
