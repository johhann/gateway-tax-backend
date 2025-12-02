<?php

namespace App\Filament\Widgets;

use App\Enums\InformationSource;
use App\Enums\ProfileUserStatus;
use App\Models\Profile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class ProfileReferralChart extends ChartWidget
{
    protected ?string $heading = 'Profile Referrals by Source';

    protected static ?int $sort = 30;

    // protected int|string|array $columnSpan = 'full';
    // protected ?string $maxHeight = '500px';

    protected function getData(): array
    {
        return Cache::remember('profile-referral-chart', now()->addMinutes(15), function () {
            // Get all enum values
            $sources = InformationSource::values();

            // Count profiles grouped by source
            $counts = Profile::query()
                ->whereNot('status', ProfileUserStatus::DRAFT)
                ->selectRaw('hear_from, COUNT(*) as total')
                ->groupBy('hear_from')
                ->pluck('total', 'hear_from')
                ->toArray();

            // Prepare chart labels and dataset
            $labels = [];
            $data = [];

            foreach ($sources as $source) {
                $labels[] = ucfirst($source);
                $data[] = $counts[$source] ?? 0;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Referrals',
                        'data' => $data,
                        'backgroundColor' => '#0d6efd',
                        'borderColor' => '#0a58ca',
                        'borderWidth' => 1,
                    ],
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
