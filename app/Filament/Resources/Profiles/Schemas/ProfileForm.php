<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('tax_station_id')
                    ->relationship('taxStation', 'name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('phone')
                    ->required(),
                DateTimePicker::make('date_of_birth')
                    ->required(),
                TextInput::make('zip_code')
                    ->required(),
                TextInput::make('hear_from')
                    ->required(),
                TextInput::make('occupation')
                    ->required(),
                Toggle::make('self_employment_income')
                    ->required(),
            ]);
    }
}
