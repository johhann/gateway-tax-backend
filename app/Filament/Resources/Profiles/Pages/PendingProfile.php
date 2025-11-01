<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\Page;

class PendingProfile extends Page
{
    protected static string $resource = ProfileResource::class;

    protected string $view = 'filament.resources.profiles.pages.pending-profile';

    protected static bool $shouldRegisterNavigation = false; // Hide from main navigation

    public static function routeName(): string
    {
        return '/profiles/{record}/pending';
    }
}
