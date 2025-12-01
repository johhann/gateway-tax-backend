<?php

namespace App\Filament\Resources\TaxRequests\Pages;

use App\Filament\Resources\TaxRequests\TaxRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaxRequest extends ViewRecord
{
    protected static string $resource = TaxRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            TaxRequestResource::assignUserAction(),
            TaxRequestResource::uploadFileAction(),
            TaxRequestResource::changeStatus(),
            TaxRequestResource::markAsReadyForPickup(),
            TaxRequestResource::markAsCompleted(),
        ];
    }
}
