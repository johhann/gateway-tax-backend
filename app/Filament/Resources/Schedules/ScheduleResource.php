<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Schedules\Pages\ViewSchedule;
use App\Filament\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Schedules\Schemas\ScheduleInfolist;
use App\Filament\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Branch;
use App\Models\Schedule;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table)
            ->recordActionsPosition()
            ->recordActions([
                ActionGroup::make([
                    self::assignBranchAction(),
                    self::assignUserAction(),
                ])
                    ->icon('heroicon-o-adjustments-horizontal'),
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'view' => ViewSchedule::route('/{record}'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                // SoftDeletingScope::class,
            ]);
    }

    public static function assignBranchAction(): Action
    {
        return Action::make('Assign Branch')
            ->visible(fn () => (auth()->user()->isOperation() || auth()->user()->isAdmin()))
            // ->slideOver()
            ->label(fn ($record) => $record->branch_id ? 'Change Branch' : 'Assign Branch')
            ->color(fn ($record) => $record->branch_id ? 'warning' : 'primary')
            ->icon('heroicon-o-building-office')
            ->modalWidth('md')
            ->schema([
                Select::make('branch_id')
                    ->label('Branch')
                    ->options(Branch::pluck('name', 'id'))
                    ->default(fn ($record) => $record->branch_id)
                    ->required()
                    ->searchable(),
            ])
            ->action(function (Schedule $record, array $data): void {
                $record->update([
                    'branch_id' => $data['branch_id'],
                ]);

                // Notify Branch
                $branchManagers = User::active()
                    ->branchManager()
                    ->where('branch_id', $record->branch_id)
                    ->get();

                $record = $record->refresh();

                Notification::make()
                    ->title('Schedule Assigned')
                    ->body('Schedule has been assigned to '.$record->branch->name.' branch. Please check the schedule.')
                    ->success()
                    ->actions([
                        Action::make('view schedule')
                            ->button()
                            ->url(fn () => '/schedules/'.$record->id),
                    ])
                    ->sendToDatabase($branchManagers)
                    ->send();
            });
    }

    public static function assignUserAction(): Action
    {
        return Action::make('Assign User')
            ->visible(fn ($record) => $record->branch_id && (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()))
            // ->slideOver()
            ->label(fn ($record) => $record->assigned_user_id ? 'Change User' : 'Assign User')
            ->color(fn ($record) => $record->assigned_user_id ? 'warning' : 'primary')
            ->icon('heroicon-o-building-office')
            ->modalWidth('md')
            ->schema([
                Select::make('assigned_user_id')
                    ->label('User')
                    ->options(fn ($record) => User::accountant()->where('branch_id', $record->branch_id)->get()->pluck('name', 'id'))
                    ->default(fn ($record) => $record->assigned_user_id)
                    ->required()
                    ->searchable(),
            ])
            ->action(function (Schedule $record, array $data): void {
                $record->update([
                    'assigned_user_id' => $data['assigned_user_id'],
                ]);
                $record = $record->refresh();

                Notification::make()
                    ->title('Schedule Assigned')
                    ->body('Schedule has been assigned to '.$record->branch->name.' branch. Please check the schedule.')
                    ->success()
                    ->actions([
                        Action::make('view schedule')
                            ->button()
                            ->url(fn () => '/schedules/'.$record->id),
                    ])
                    ->sendToDatabase($record->assignedTo)
                    ->send();
            });
    }
}
