<?php

namespace App\Filament\Resources\TaxStations\Pages;

use App\Filament\Resources\TaxStations\TaxStationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxStations extends ListRecords
{
    protected static string $resource = TaxStationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
