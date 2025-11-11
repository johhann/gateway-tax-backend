<?php

namespace App\Filament\Resources\TaxRequests\Pages;

use App\Filament\Resources\TaxRequests\TaxRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxRequests extends ListRecords
{
    protected static string $resource = TaxRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
