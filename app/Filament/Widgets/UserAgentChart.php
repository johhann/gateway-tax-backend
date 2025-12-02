<?php

namespace App\Filament\Widgets;

use App\Enums\UserAgentEnum;
use App\Enums\UserRole;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class UserAgentChart extends ChartWidget
{
    protected ?string $heading = 'User Agents';

    protected static ?int $sort = 31;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return Cache::remember('user-agent-chart', now()->addMinutes(15), function () {
            // Get all enum values
            $userAgents = UserAgentEnum::values();

            // Count profiles grouped by source
            $counts = User::query()
                ->where('role', UserRole::USER)
                ->selectRaw('user_agent, COUNT(*) as total')
                ->groupBy('user_agent')
                ->pluck('total', 'user_agent')
                ->toArray();

            // Prepare chart labels and dataset
            $labels = [];
            $data = [];

            foreach ($userAgents as $userAgent) {
                $labels[] = ucfirst($userAgent);
                $data[] = $counts[$userAgent] ?? 0;
            }

            // Define more vibrant colors corresponding to the enum order (ios, web, android)
            $backgroundColors = [
                '#0EA5E9', // iOS: Vibrant sky blue
                '#FCD34D', // Web: Bright lemon yellow
                '#34D399', // Android: Vibrant emerald green
            ];

            $borderColors = [
                '#0284C7', // Darker sky blue for iOS
                '#EAB308', // Darker yellow for Web
                '#10B981', // Darker emerald for Android
            ];

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Referrals',
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 1,
                    ],
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'x',
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
