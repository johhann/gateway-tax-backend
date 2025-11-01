<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'profiles';

    protected static ?string $relatedResource = ProfileResource::class;

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Profiles')
            ->badge($ownerRecord->profiles()->count())
            ->badgeColor('info')
            ->badgeTooltip('The number of posts in this category')
            ->icon('heroicon-o-user');
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
