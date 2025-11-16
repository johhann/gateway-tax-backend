<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::withoutGlobalScopes()->pluck('id');
        $branches = Branch::withoutGlobalScopes()->pluck('id');

        return [
            'user_id' => fake()->randomElement($users),
            'scheduled_start_time' => $date = fake()->dateTimeBetween('-2 days', '1 week'),
            'scheduled_end_time' => Carbon::instance($date)->addHour(),
            'type' => $type = fake()->randomElement(ScheduleType::cases()),
            'status' => fake()->randomElement(ScheduleStatus::cases()),
            'branch_id' => $type === ScheduleType::OnlineCall ?: fake()->randomElement($branches),
        ];
    }
}
