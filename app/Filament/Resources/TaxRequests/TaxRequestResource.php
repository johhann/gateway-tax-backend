<?php

namespace App\Filament\Resources\TaxRequests;

use App\Enums\TaxRequestStatus;
use App\Filament\Resources\TaxRequests\Pages\CreateTaxRequest;
use App\Filament\Resources\TaxRequests\Pages\EditTaxRequest;
use App\Filament\Resources\TaxRequests\Pages\ListTaxRequests;
use App\Filament\Resources\TaxRequests\Pages\ViewTaxRequest;
use App\Filament\Resources\TaxRequests\Schemas\TaxRequestForm;
use App\Filament\Resources\TaxRequests\Schemas\TaxRequestInfolist;
use App\Filament\Resources\TaxRequests\Tables\TaxRequestsTable;
use App\Models\TaxRequest;
use App\Models\User;
use App\Notifications\TaxRequestReadyForPickupNotification;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
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
                    self::uploadFileAction(),
                    self::changeStatus(),
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

    public static function uploadFileAction(): Action
    {
        return Action::make('Upload File')
            ->color('secondary')
            ->visible(fn () => (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()))
            ->icon('heroicon-o-plus')
            ->modalWidth('xl')
            ->schema(function (TaxRequest $record) {
                return [
                    SpatieMediaLibraryFileUpload::make('file')
                        ->collection('tax_requests')
                        ->multiple()
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->maxFiles(10)
                        ->reorderable(),
                ];
            })
            ->action(function (TaxRequest $record): void {
                // Media attachments are handled automatically by the component
                Notification::make()
                    ->title('Files uploaded successfully')
                    ->success()
                    ->send();
            });
    }

    public static function changeStatus(): Action
    {
        return Action::make('Update Status')
            ->color('primary')
            ->visible(fn ($record) => (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()) && $record->status !== TaxRequestStatus::Pending)
            ->hidden(fn ($record) => $record->status === TaxRequestStatus::ReadyForPickup)
            ->icon('heroicon-o-flag')
            ->modalWidth('xl')
            ->slideOver()
            ->schema(function (TaxRequest $record) {
                return [
                    Select::make('status')
                        ->label('Status')
                        ->default($record->status)
                        ->options(
                            collect(TaxRequestStatus::cases())
                                ->filter(
                                    fn (TaxRequestStatus $status) => in_array($status, [
                                        TaxRequestStatus::Processing,
                                        TaxRequestStatus::Processed,
                                        TaxRequestStatus::Canceled,
                                    ])
                                )
                                ->mapWithKeys(fn (TaxRequestStatus $status) => [
                                    $status->value => $status->name, // Or use a custom label method if implemented in the enum
                                ])
                        )
                        ->required(),
                ];
            })
            ->action(function (TaxRequest $record, array $data): void {

                // update status
                $record->update([
                    'status' => $data['status'],
                ]);

                // Media attachments are handled automatically by the component
                Notification::make()
                    ->title('Tax Request Status marked as '.$record->status->value)
                    ->success()
                    ->send();
            });
    }

    public static function markAsReadyForPickup(): Action
    {
        return Action::make('Mark as Ready for Pickup')
            ->color('info')
            ->visible(fn ($record) => $record->media && $record->status === TaxRequestStatus::Processed)
            ->icon('heroicon-o-check-circle')
            // ->modalWidth('xl')
            ->requiresConfirmation()
            ->action(function (TaxRequest $record): void {

                // update status
                $record->update([
                    'status' => TaxRequestStatus::ReadyForPickup,
                ]);

                // Media attachments are handled automatically by the component
                Notification::make()
                    ->title('Tax Request is marked as ready for pickup')
                    ->success()
                    ->send();

                // send notification to user
                $record->user->notify(new TaxRequestReadyForPickupNotification($record));
            });
    }

    public static function markAsCompleted(): Action
    {
        return Action::make('Mark as Completed')
            ->color('success')
            ->visible(fn ($record) => $record->status === TaxRequestStatus::ReadyForPickup)
            ->icon('heroicon-o-check-circle')
            // ->modalWidth('xl')
            ->requiresConfirmation()
            ->action(function (TaxRequest $record): void {

                // update status
                $record->update([
                    'status' => TaxRequestStatus::Completed,
                ]);

                // Media attachments are handled automatically by the component
                Notification::make()
                    ->title('Tax Request is marked as ready for pickup')
                    ->success()
                    ->send();

                // send notification to user
                $record->user->notify(new TaxRequestReadyForPickupNotification($record));
            });
    }
}
