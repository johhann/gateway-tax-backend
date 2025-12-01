<?php

namespace App\Filament\Widgets;

use App\Enums\TaxRequestStatus;
use App\Models\TaxRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class PriorYearTaxRequests extends ChartWidget
{
    protected ?string $heading = 'Prior-Year Tax Return Requests';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        return Cache::remember('tax-requests-prior-year', now()->addMinutes(15), function () {
            // Get counts grouped by status
            $results = TaxRequest::select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            // Ensure labels follow enum order, even if some have zero
            $labels = [];
            $data = [];
            $colors = [];

            foreach (TaxRequestStatus::cases() as $case) {
                $labels[] = ucfirst(str_replace('_', ' ', $case->value));
                $data[] = $results[$case->value] ?? 0;
                $colors[] = $this->mapEnumColorToChartColor($case->color());
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Requests',
                        'data' => $data,
                        'backgroundColor' => $colors,
                    ],
                ],
            ];
        });
    }

    /**
     * Convert Filament color names (danger, warning, info, etc.)
     * to actual chart colors.
     */
    protected function mapEnumColorToChartColor(string $colorName): string
    {
        return match ($colorName) {
            'danger' => '#dc3545',
            'warning' => '#ffc107',
            'success' => '#198754',
            'info' => '#0dcaf0',
            'secondary' => '#6c757d',
            default => '#0d6efd', // primary (fallback)
        };
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '260px';
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
