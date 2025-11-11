<?php

namespace App\Filament\Resources\TaxRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TaxRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('user.id')
                    ->state(fn ($record) => $record->user->name)
                    ->searchable(),
                TextColumn::make('tax_year')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('ssn')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => $record->status->color())
                    ->searchable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
