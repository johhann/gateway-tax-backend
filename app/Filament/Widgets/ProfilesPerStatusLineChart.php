<?php

namespace App\Filament\Widgets;

use App\Enums\ProfileProgressStatus;
use App\Models\Profile;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class ProfilesPerStatusLineChart extends ChartWidget
{
    protected ?string $heading = 'Profiles Per Status (Yearly Overview)';

    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        $this->filter = (string) now()->year;
    }

    protected function getFilters(): ?array
    {
        $minYear = Profile::selectRaw("MIN(strftime('%Y', created_at)) as min_year")
            ->value('min_year');

        $minYear = $minYear ? (int) $minYear : now()->year;
        $maxYear = now()->year;

        $years = [];
        for ($y = $minYear; $y <= $maxYear; $y++) {
            $years[(string) $y] = (string) $y;
        }

        return $years;
    }

    protected function getData(): array
    {
        $year = (int) ($this->filter ?? now()->year);

        return Cache::remember("profiles-per-status-line-chart-{$year}", now()->addMinutes(15), function () use ($year) {

            // 12 months
            $months = collect(range(1, 12))->map(
                fn ($m) => Carbon::createFromDate($year, $m, 1)->format('M')
            )->toArray();

            $datasets = [];

            foreach (ProfileProgressStatus::cases() as $status) {

                $query = Profile::selectRaw("strftime('%m', created_at) as month, COUNT(*) as total")
                    ->whereYear('created_at', $year)
                    ->where('progress_status', $status->value)
                    ->groupByRaw("strftime('%m', created_at)")
                    ->pluck('total', 'month');

                // Fill missing months (01 → 12)
                $data = [];
                for ($m = 1; $m <= 12; $m++) {
                    $key = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $data[] = $query[$key] ?? 0;
                }

                $datasets[] = [
                    'label' => $status->label(),
                    'data' => $data,
                    'borderColor' => $this->mapColor($status->color()),
                    'backgroundColor' => $this->mapColor($status->color()).'33',
                    'tension' => 0.3,
                ];
            }

            return [
                'labels' => $months,
                'datasets' => $datasets,
            ];
        });
    }

    protected function getMaxHeight(): ?string
    {
        return '300px';
    }

    protected function mapColor(string $color): string
    {
        return match ($color) {
            'danger' => '#dc3545',
            'warning' => '#ffc107',
            'success' => '#198754',
            'info' => '#0dcaf0',
            'secondary' => '#6c757d',
            default => '#0d6efd',
        };
    }

    protected function getType(): string
    {
        return 'line';
    }
}
