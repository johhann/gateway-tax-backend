<?php

namespace Database\Seeders;

use App\Models\TaxStation;
use Illuminate\Database\Seeder;

class TaxStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            ['name' => 'Gateway Tax Service', 'status' => true],
            ['name' => 'Jackson Hewitt', 'status' => true],
            ['name' => 'H&R Block', 'status' => true],
            ['name' => 'Liberty', 'status' => true],
            ['name' => 'Turbo', 'status' => true],
            ['name' => 'Other', 'status' => true],
        ];

        foreach ($stations as $station) {
            TaxStation::updateOrCreate(
                ['name' => $station['name']],
                ['status' => $station['status']]
            );
        }
    }
}
