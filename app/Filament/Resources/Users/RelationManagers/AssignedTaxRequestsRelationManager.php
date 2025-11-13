<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\TaxRequests\TaxRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AssignedTaxRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedTaxRequests';

    protected static ?string $relatedResource = TaxRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Tax Requests')
            ->badge($ownerRecord->assignedTaxRequests()->count())
            ->badgeColor('info')
            ->badgeTooltip('The number of tax requests assigned to this user')
            ->icon('heroicon-o-rectangle-stack');
    }
}
