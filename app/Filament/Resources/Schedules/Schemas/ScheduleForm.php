<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('scheduled_start_time')
                    ->required(),
                DateTimePicker::make('scheduled_end_time')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
            ]);
    }
}
