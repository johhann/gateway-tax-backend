<?php

namespace App\Filament\Resources\TaxRequests\Schemas;

use App\Enums\TaxRequestStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaxRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                TextInput::make('tax_year')
                    ->required()
                    ->numeric(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('ssn')
                    ->required(),
                Textarea::make('specific_request')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(TaxRequestStatus::class)
                    ->required(),
            ]);
    }
}
