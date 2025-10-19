<?php

namespace App\Filament\Resources\TaxStations\Pages;

use App\Filament\Resources\TaxStations\TaxStationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTaxStation extends EditRecord
{
    protected static string $resource = TaxStationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
