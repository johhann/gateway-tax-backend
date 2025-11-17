<?php

namespace Database\Factories;

use App\Enums\MeetingType;
use App\Enums\ScheduleSession;
use App\Enums\ScheduleStatus;
use App\Enums\UserRole;
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
        $users = User::withoutGlobalScopes()->where('role', UserRole::USER)->pluck('id');
        $branches = Branch::withoutGlobalScopes()->pluck('id');

        $date = fake()->dateTimeBetween('-2 days', '1 week');
        $type = fake()->randomElement(MeetingType::cases());

        return [
            'user_id' => fake()->randomElement($users),
            'scheduled_start_time' => $date,
            'scheduled_end_time' => Carbon::instance($date)->addHour(),
            'type' => $type->value,
            'session' => fake()->randomElement(ScheduleSession::cases())->value,
            'status' => fake()->randomElement(ScheduleStatus::cases())->value,
            'branch_id' => $type === MeetingType::OnlineCall ? null : fake()->randomElement($branches),
        ];
    }
}
