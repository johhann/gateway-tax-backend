<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Enums\ProfileProgressStatus;
use App\Enums\ProfileUserStatus;
use App\Filament\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // Get all distinct statuses from DB
        $existing = Profile::distinct()->pluck('progress_status')->toArray();

        $tabs = [
            'all' => Tab::make('All')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNot('user_status', ProfileUserStatus::DRAFT))
                ->badge(fn () => Profile::query()->whereNot('user_status', ProfileUserStatus::DRAFT)->count())
                ->badgeColor('gray'),
        ];

        // Get enum cases, filter to those existing in DB, then sort by order()
        $statuses = collect(ProfileProgressStatus::cases())
            ->filter(fn ($status) => in_array($status, $existing, true))
            ->sortBy(fn ($status) => $status->order());

        foreach ($statuses as $status) {
            $tabs[$status->value] = Tab::make($status->value)
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('progress_status', $status->value)
                )
                ->badge(fn () => Profile::where('progress_status', $status->value)->count())
                ->badgeColor($status->color());
        }

        return $tabs;
    }
}
