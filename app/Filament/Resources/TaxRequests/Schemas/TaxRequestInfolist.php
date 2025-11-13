<?php

namespace App\Filament\Resources\TaxRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaxRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('tax_year'),
                TextEntry::make('full_name')
                    ->label('Requested Name'),
                TextEntry::make('ssn')
                    ->label('SSN'),
                TextEntry::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('specific_request')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
