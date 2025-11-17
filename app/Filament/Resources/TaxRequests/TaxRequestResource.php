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
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxRequestResource extends Resource
{
    protected static ?string $model = TaxRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentChartBar;

    protected static ?string $recordTitleAttribute = 'full_name';

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
        return TaxRequestsTable::configure($table)
            ->recordActionsPosition()
            ->recordActions([
                ActionGroup::make([
                    self::assignUserAction(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
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

    public static function assignUserAction(): Action
    {
        return Action::make('Assign Accountant')
            ->label(fn ($record) => $record->assigned_user_id ? 'Change Accountant' : 'Assign Accountant')
            ->color(fn ($record) => $record->assigned_user_id ? 'warning' : 'success')
            ->visible(fn () => (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()))
            ->slideOver()
            ->icon('heroicon-o-plus')
            ->modalWidth('sm')
            ->schema(function ($record) {
                return [
                    Select::make('assigned_user_id')
                        ->label('Users')
                        ->default($record->assigned_user_id)
                        ->options(function () {
                            $query = User::query()
                                ->accountant();

                            if (auth()->user()->isBranchManager()) {
                                $query->where('branch_id', auth()->user()->branch_id);
                            }

                            return $query->get()->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),
                ];
            })
            ->action(function (TaxRequest $record, array $data): void {
                $record->update([
                    'assigned_user_id' => $data['assigned_user_id'],
                ]);
            });
    }
}
