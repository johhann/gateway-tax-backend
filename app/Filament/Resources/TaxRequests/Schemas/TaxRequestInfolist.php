<?php

namespace App\Filament\Resources\TaxRequests\Schemas;

use App\Models\TaxRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaxRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.id')
                    ->label('User'),
                TextEntry::make('tax_year')
                    ->numeric(),
                TextEntry::make('full_name'),
                TextEntry::make('ssn'),
                TextEntry::make('specific_request')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (TaxRequest $record): bool => $record->trashed()),
            ]);
    }
}
