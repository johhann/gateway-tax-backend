<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Models\Schedule;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('branch.name')
                    ->placeholder('-')
                    ->label('Branch'),
                TextEntry::make('scheduled_start_time')
                    ->dateTime('M d, Y h:s A'),
                TextEntry::make('scheduled_end_time')
                    ->dateTime('M d, Y h:s A'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('status')
                    ->color(fn (Schedule $record) => $record->status->color()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
