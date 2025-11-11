<?php

namespace Database\Factories;

use App\Enums\TaxRequestStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxRequest>
 */
class TaxRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck('id');

        return [
            'user_id' => $this->faker->randomElement($users),
            'tax_year' => $this->faker->year(),
            'full_name' => $this->faker->name(),
            'ssn' => $this->faker->unique()->ssn(),
            'status' => $this->faker->randomElement(TaxRequestStatus::cases()),
        ];
    }
}
