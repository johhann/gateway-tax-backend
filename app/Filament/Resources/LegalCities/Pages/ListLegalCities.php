<?php

namespace App\Filament\Resources\LegalCities\Pages;

use App\Filament\Resources\LegalCities\LegalCityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLegalCities extends ListRecords
{
    protected static string $resource = LegalCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
