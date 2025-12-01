<?php

namespace App\Filament\Widgets;

use App\Enums\ProfileProgressStatus;
use App\Models\Profile;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LastFiveProfilesTable extends TableWidget
{
    protected static ?int $sort = 12;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Profile::query()->with('assignedTo')->orderBy('created_at', 'desc')->limit(5))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')
                    ->state(fn ($record) => $record->name)
                    ->description(fn ($record) => $record->phone),
                TextColumn::make('branch.name')
                    ->placeholder('-')
                    ->description(fn ($record) => $record->assignedTo?->name)
                    ->hidden(auth()->user()->isAccountant() || auth()->user()->isBranchManager())
                    ->limit(20),
                TextColumn::make('self_employment_income')
                    ->label('Self Employed')
                    ->state(fn ($record) => $record->self_employment_income ? 'Yes' : 'No')
                    ->color(fn ($record) => $record->self_employment_income ? Color::Green : Color::Red),
                TextColumn::make('progress_status')
                    ->badge()
                    ->color(
                        fn ($record) => ProfileProgressStatus::from($record->progress_status->value)->color()
                    ),
                TextColumn::make('user_status')
                    ->color(fn ($record) => $record->user_status->color()),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('View All')
                    ->button()
                    ->url(fn (): string => '/profiles'),
            ])
            ->recordActions([
                //
            ])
            ->paginated(false);
    }
}
