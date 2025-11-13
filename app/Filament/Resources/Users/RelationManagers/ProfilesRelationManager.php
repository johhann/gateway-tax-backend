<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedProfiles';

    protected static ?string $relatedResource = ProfileResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Profiles')
            ->badge($ownerRecord->assignedProfiles()->count())
            ->badgeColor('info')
            ->badgeTooltip('The number of profiles assigned to this user')
            ->icon('heroicon-o-identification');
    }
}
