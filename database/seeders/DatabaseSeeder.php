<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
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
            TaxStationSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Gateway Manger',
            'email' => 'admin@gateway.com',
            'role' => UserRole::ADMIN,
            'password' => bcrypt('password'),
        ]);

        User::factory(50)->create();

        $this->call([
            ProfileSeeder::class,
        ]);

        DB::commit();
    }
}
