<?php

namespace App\Filament\Resources\LegalCities;

use App\Filament\Resources\LegalCities\Pages\CreateLegalCity;
use App\Filament\Resources\LegalCities\Pages\EditLegalCity;
use App\Filament\Resources\LegalCities\Pages\ListLegalCities;
use App\Filament\Resources\LegalCities\RelationManagers\LocationsRelationManager;
use App\Filament\Resources\LegalCities\Schemas\LegalCityForm;
use App\Filament\Resources\LegalCities\Tables\LegalCitiesTable;
use App\Models\LegalCity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegalCityResource extends Resource
{
    protected static ?string $model = LegalCity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;

    protected static ?string $navigationLabel = 'City';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LegalCityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalCitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalCities::route('/'),
            'create' => CreateLegalCity::route('/create'),
            'edit' => EditLegalCity::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
