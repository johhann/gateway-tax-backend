<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::beginTransaction();

        $this->call([
            LegalCitySeeder::class,
            BranchSeeder::class,
            TaxStationSeeder::class,
            UserSeeder::class,
            ProfileSeeder::class,
            TaxRequestSeeder::class,
        ]);

        DB::commit();
    }
}
