<?php

namespace App\Filament\Resources\TaxStations;

use App\Filament\Resources\TaxStations\Pages\CreateTaxStation;
use App\Filament\Resources\TaxStations\Pages\EditTaxStation;
use App\Filament\Resources\TaxStations\Pages\ListTaxStations;
use App\Filament\Resources\TaxStations\Schemas\TaxStationForm;
use App\Filament\Resources\TaxStations\Tables\TaxStationsTable;
use App\Models\TaxStation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxStationResource extends Resource
{
    protected static ?string $model = TaxStation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TaxStationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxStationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxStations::route('/'),
            'create' => CreateTaxStation::route('/create'),
            'edit' => EditTaxStation::route('/{record}/edit'),
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
