<?php

namespace App\Filament\Resources\Profiles;

use App\Enums\ProfileProgressStatus;
use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Resources\Profiles\RelationManagers\AddressRelationManager;
use App\Filament\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Resources\Profiles\Schemas\ProfileInfolist;
use App\Filament\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Branch;
use App\Models\Profile;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Identification;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilesTable::configure($table)
            ->recordActionsPosition()
            ->recordActions([
                ActionGroup::make([
                    self::assignBranchAction(),
                    self::assignAccountantAction(),
                    // self::markAsProcessed(),
                    self::changeStatus(),
                ])
                    ->icon('heroicon-o-adjustments-horizontal'),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function changeStatus(): Action
    {
        return Action::make('Update Status')
            // ->visible(fn (Profile $record) => (auth()->user()->isOperation() || auth()->user()->isAdmin()))
            ->slideOver()
            ->color('warning')
            ->icon('heroicon-o-flag')
            ->modalWidth('sm')
            ->schema([
                Select::make('progress_status')
                    ->options(ProfileProgressStatus::class)
                    ->default(fn (Profile $record) => $record->progress_status)
                    ->required(),
            ])
            ->action(function (Profile $record, array $data): void {
                $record->update([
                    'progress_status' => $data['progress_status'],
                ]);
            });
    }

    public static function assignBranchAction(): Action
    {
        return Action::make('Assign Branch')
            ->visible(fn (Profile $record) => (auth()->user()->isOperation() || auth()->user()->isAdmin()))
            ->slideOver()
            ->label(fn ($record) => $record->assigned_branch_id ? 'Change Branch' : 'Assign Branch')
            ->color(fn ($record) => $record->assigned_branch_id ? 'secondary' : 'primary')
            ->icon('heroicon-o-building-office')
            ->modalWidth('sm')
            ->schema([
                Select::make('branch_id')
                    ->label('Branch')
                    ->options(Branch::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
            ])
            ->action(function (Profile $record, array $data): void {
                $record->update([
                    'assigned_branch_id' => $data['branch_id'],
                ]);
            });
    }

    public static function assignAccountantAction(): Action
    {
        return Action::make('Assign Accountant')
            ->visible(fn (Profile $record) => (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()) && $record->assigned_branch_id)
            ->slideOver()
            ->label(fn ($record) => $record->assigned_user_id ? 'Change Accountant' : 'Assign Accountant')
            ->color(fn ($record) => $record->assigned_user_id ? 'secondary' : 'primary')
            ->icon('heroicon-o-user')
            ->modalWidth('sm')
            ->schema(function (Profile $record) {
                return [
                    Select::make('assigned_user_id')
                        ->label('Accountant')
                        ->default($record->assigned_user_id)
                        ->options(User::accountant()->where('branch_id', $record->assigned_branch_id)->get()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                ];
            })
            ->action(function (Profile $record, array $data): void {
                $record->update([
                    'assigned_user_id' => $data['assigned_user_id'],
                    'progress_status' => ProfileProgressStatus::ASSIGNED,
                ]);
            });
    }

    public static function markAsProcessed(): Action
    {
        return Action::make('Mark as Processed')
            ->visible(fn (Profile $record) => $record->progress_status === ProfileProgressStatus::ASSIGNED)
            ->color('danger')
            ->icon('heroicon-o-document-text')
            ->action(function (Profile $record, array $data): void {
                $record->progress_status = ProfileProgressStatus::PROCESSED;
                $record->save();

                // send notification
                Notification::make()
                    ->title('Profile Processed')
                    ->body('Profile with ID: '.$record->id.', has been processed completed.')
                    ->success()
                    ->actions([
                        Action::make('view profile')
                            ->button()
                            ->url(fn () => '/profiles/'.$record->id),
                    ])
                    ->sendToDatabase(Auth::user())
                    ->send();
            });
    }

    public static function pause(): Action
    {
        return Action::make('Mark as Processed')
            ->visible(fn (Profile $record) => $record->progress_status === ProfileProgressStatus::ASSIGNED)
            ->color('danger')
            ->icon('heroicon-o-document-text')
            ->action(function (Profile $record, array $data): void {
                $record->update([
                    'assigned_user_id' => $data['assigned_user_id'],
                ]);
            });
    }

    public static function submitAction(): Action
    {
        return Action::make('Submit Profile')
            // ->visible(fn (Profile $record) => (auth()->user()->isOperation() || auth()->user()->isAdmin() || auth()->user()->isBranchManager()) && $record->assigned_branch_id)
            // ->slideOver()
            ->color('primary')
            ->icon('heroicon-o-check-badge')
            ->modalWidth('md')
            ->requiresConfirmation()
            ->action(function (Profile $record, array $data): void {
                // submit service

                // send notification
                Notification::make()
                    ->title('Profile Submission Requested')
                    ->body('Profile submission request for ID: '.$record->id.', is initiated. We will notify you once it is completed.')
                    ->success()
                    ->actions([
                        Action::make('view profile')
                            ->button()
                            ->url(fn () => '/profiles/'.$record->id),
                    ])
                    ->sendToDatabase(Auth::user())
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            // AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'create' => CreateProfile::route('/create'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         Pages\CreateProfile::class,
    //         Pages\ViewProfile::class,
    //     ]);
    // }
}
