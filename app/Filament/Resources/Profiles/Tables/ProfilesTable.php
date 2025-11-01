<?php

namespace App\Filament\Resources\Profiles\Tables;

use App\Enums\ProfileProgressStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfilesTable
{
    public static function configure(Table $table, $status = null): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'asc'))
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->state(fn ($record) => $record->name)
                    ->searchable(true, function ($query, $search) {
                        return $query
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),
                TextColumn::make('taxStation.name')
                    ->searchable(),
                TextColumn::make('branch.name')
                    ->placeholder('-')

                    ->searchable(),
                TextColumn::make('assignedTo.name')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('self_employment_income')
                    ->label('Self Employed')
                    ->state(fn ($record) => $record->self_employment_income ? 'Yes' : 'No')
                    ->color(fn ($record) => $record->self_employment_income ? Color::Green : Color::Red),

                TextColumn::make('progress_status')
                    ->badge()
                    ->color(
                        fn ($state) => ProfileProgressStatus::from($state)->color()
                    ),
                TextColumn::make('user_status')
                    ->color(fn ($record) => $record->user_status->color()),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }
}
