<?php

namespace App\Filament\Resources\LegalCities\Pages;

use App\Filament\Resources\LegalCities\LegalCityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLegalCity extends EditRecord
{
    protected static string $resource = LegalCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
