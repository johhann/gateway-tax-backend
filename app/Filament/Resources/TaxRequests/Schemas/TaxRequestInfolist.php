<?php

namespace App\Filament\Resources\TaxRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class TaxRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Requested By')
                    ->url(fn ($record) => "/users/{$record->user_id}")
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition('end'),
                TextEntry::make('full_name')
                    ->label('Requested For'),
                TextEntry::make('tax_year'),
                TextEntry::make('ssn')
                    ->label('SSN'),
                TextEntry::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn ($record) => $record->assigned_user_id ? "/users/{$record->assigned_user_id}" : false)
                    ->placeholder('Unassigned'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('specific_request')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                ViewEntry::make('tax_requests')
                    ->label('Attached Files')
                    ->view('filament.media-gallery'),
            ]);
    }
}
