<?php

namespace App\Filament\Resources\TaxRequests;

use App\Filament\Resources\TaxRequests\Pages\CreateTaxRequest;
use App\Filament\Resources\TaxRequests\Pages\EditTaxRequest;
use App\Filament\Resources\TaxRequests\Pages\ListTaxRequests;
use App\Filament\Resources\TaxRequests\Pages\ViewTaxRequest;
use App\Filament\Resources\TaxRequests\Schemas\TaxRequestForm;
use App\Filament\Resources\TaxRequests\Schemas\TaxRequestInfolist;
use App\Filament\Resources\TaxRequests\Tables\TaxRequestsTable;
use App\Models\TaxRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxRequestResource extends Resource
{
    protected static ?string $model = TaxRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'user_id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return TaxRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaxRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxRequestsTable::configure($table);
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
            'index' => ListTaxRequests::route('/'),
            'create' => CreateTaxRequest::route('/create'),
            'view' => ViewTaxRequest::route('/{record}'),
            'edit' => EditTaxRequest::route('/{record}/edit'),
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
