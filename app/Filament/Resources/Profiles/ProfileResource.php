<?php

namespace App\Filament\Resources\Profiles;

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
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
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
                ]);
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
