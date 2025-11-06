<?php

namespace App\Filament\Resources\Profiles\Tables;

use App\Enums\ProfileProgressStatus;
use App\Enums\ProfileUserStatus;
use App\Models\Branch;
use App\Models\TaxStation;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfilesTable
{
    public static function configure(Table $table, $status = null): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('id', 'desc'))
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
                    ->hidden(auth()->user()->isAccountant() || auth()->user()->isBranchManager())
                    ->searchable(),
                TextColumn::make('assignedTo.name')
                    ->placeholder('-')
                    ->hidden(auth()->user()->isAccountant())
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
                SelectFilter::make('progress_status')
                    ->label('Status')
                    ->multiple()
                    ->options(ProfileProgressStatus::class),
                SelectFilter::make('user_status')
                    ->label('User Status')
                    ->multiple()
                    ->options(ProfileUserStatus::class),
                SelectFilter::make('taxStation')
                    ->label('Tax Station')
                    ->relationship('taxStation', 'name')
                    ->multiple()
                    ->options(TaxStation::pluck('name', 'id')),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->placeholder('Select start date'),
                        DatePicker::make('to')
                            ->label('To Date')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Date ≥ '.$data['from'];
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = 'Date ≤ '.$data['to'];
                        }

                        return $indicators;
                    })->columns(2)
                    ->columnSpan(2),
                SelectFilter::make('self_employment_income')
                    ->label('Self Employed')
                    ->options([true => 'Yes', false => 'No']),
                SelectFilter::make('branch')
                    ->relationship('branch', 'name')
                    ->multiple()
                    ->options(Branch::pluck('name', 'id'))
                    ->visible(auth()->user()->isAdmin() || auth()->user()->isOperation()),
                SelectFilter::make('assignedTo')
                    ->relationship('assignedTo', 'first_name')
                    ->multiple()
                    ->options(User::accountant()->get(['first_name', 'middle_name', 'last_name', 'id'])->pluck('name', 'id'))
                    ->visible(auth()->user()->isAdmin() || auth()->user()->isOperation() || auth()->user()->isBranchManager()),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormMaxHeight('100px')
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
