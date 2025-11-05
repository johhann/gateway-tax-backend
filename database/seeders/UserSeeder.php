<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'admin@gateway.com',
            'role' => UserRole::ADMIN,
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'operation@gateway.com',
            'role' => UserRole::OPERATION,
            'password' => bcrypt('password'),
        ]);

        User::factory(10)->create(['role' => UserRole::BRANCH_MANAGER]);
        User::factory(50)->create(['role' => UserRole::ACCOUNTANT]);
        User::factory(100)->create(['role' => UserRole::USER, 'branch_id' => null]);
    }
}
