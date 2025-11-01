<?php

namespace Database\Factories;

use App\Enums\ProfileProgressStatus;
use App\Enums\ProfileUserStatus;
use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\TaxStation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taxStations = TaxStation::pluck('id');
        $users = User::where('role', UserRole::USER)->pluck('id');
        $accountants = User::where('role', UserRole::ACCOUNTANT)->get();
        $branches = Branch::pluck('id');

        return [
            'user_id' => $this->faker->unique()->randomElement($users),
            'tax_station_id' => $this->faker->randomElement($taxStations),
            'assigned_branch_id' => $branch = $this->faker->randomElement($branches),
            'assigned_user_id' => $accountants->where('branch_id', $branch)->random(),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'zip_code' => $this->faker->postcode,
            'hear_from' => $this->faker->randomElement(['Social Media', 'Friend', 'Advertisement', 'Website']),
            'occupation' => $this->faker->jobTitle,
            'self_employment_income' => $this->faker->boolean(60), // 30% chance of true
            'progress_status' => $this->faker->randomElement(ProfileProgressStatus::getValues()),
            'user_status' => $this->faker->randomElement(ProfileUserStatus::getValues()),
        ];
    }
}
