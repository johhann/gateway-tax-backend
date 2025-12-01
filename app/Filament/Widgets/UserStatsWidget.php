<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class UserStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return Cache::remember('users-stats', now()->addMinutes(5), function () {

            return [
                Stat::make(
                    'Total Registered Users',
                    User::query()
                        ->withoutGlobalScopes()
                        ->where('role', UserRole::USER)
                        ->count()
                )
                    ->description('All users in the system')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('primary'),

                Stat::make(
                    'New Sign-Ups (Today)',
                    User::query()
                        ->withoutGlobalScopes()
                        ->where('role', UserRole::USER)->whereDate('created_at', Carbon::today())->count()
                )
                    ->description('Joined today')
                    ->descriptionIcon('heroicon-o-sparkles')
                    ->color('success'),

                Stat::make(
                    'New Sign-Ups (This Week)',
                    User::query()
                        ->withoutGlobalScopes()->where('role', UserRole::USER)->whereBetween('created_at', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek(),
                        ])->count()
                )
                    ->description('Joined this week')
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('info'),

                Stat::make(
                    'New Sign-Ups (This Month)',
                    User::query()
                        ->withoutGlobalScopes()
                        ->where('role', UserRole::USER)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->count()
                )
                    ->description('Joined this month')
                    ->descriptionIcon('heroicon-o-chart-bar')
                    ->color('warning'),
            ];
        });
    }
}
