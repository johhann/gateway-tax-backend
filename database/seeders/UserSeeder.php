<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::pluck('id');
        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'admin@gateway.com',
            'role' => UserRole::ADMIN,
            'password' => bcrypt('password'),
            'branch_id' => null,
        ]);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'operation@gateway.com',
            'role' => UserRole::OPERATION,
            'password' => bcrypt('password'),
            'branch_id' => null,
        ]);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'branch_manager@gateway.com',
            'role' => UserRole::BRANCH_MANAGER,
            'password' => bcrypt('password'),
            'branch_id' => $branch = $branches->random(),
        ]);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'accountant@gateway.com',
            'role' => UserRole::ACCOUNTANT,
            'password' => bcrypt('password'),
            'branch_id' => $branch,
        ]);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'user@gateway.com',
            'role' => UserRole::USER,
            'password' => bcrypt('password'),
        ]);

        User::factory(10)->create(['role' => UserRole::BRANCH_MANAGER]);
        User::factory(50)->create(['role' => UserRole::ACCOUNTANT]);
        User::factory(100)->create(['role' => UserRole::USER, 'branch_id' => null]);
    }
}
