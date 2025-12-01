<?php

namespace App\Filament\Widgets;

use App\Enums\MeetingType;
use App\Models\Schedule;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class BookingsPerLocationChart extends ChartWidget
{
    protected ?string $heading = 'Bookings Per Meeting Type';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return Cache::remember('bookings-per-meeting-type', now()->addMinutes(15), function () {
            $schedules = Schedule::select('type')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('type')
                ->get();

            $colorMap = [
                MeetingType::OnlineCall->value => '#10b981', // Tailwind success (green-500)
                MeetingType::InPersonMeeting->value => '#f59e0b', // Tailwind warning (amber-500)
            ];

            return [
                'datasets' => [
                    [
                        'label' => 'Bookings',
                        'data' => $schedules->pluck('total')->toArray(),
                        'backgroundColor' => $schedules->map(fn ($schedule) => $colorMap[$schedule->type->value])->toArray(),
                    ],
                ],
                'labels' => $schedules->map(fn ($schedule) => MeetingType::from($schedule->type->value)->name)->toArray(),
            ];
        });
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '260px'; // Limit chart height
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
