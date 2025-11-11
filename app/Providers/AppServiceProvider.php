<?php

namespace App\Providers;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::shouldBeStrict(! app()->isProduction());

        EditAction::configureUsing(function ($action) {
            return $action
                ->slideOver()
                ->iconButton();
        });

        ViewAction::configureUsing(function ($action) {
            return $action
                ->slideOver()
                ->iconButton();
        });

        DeleteAction::configureUsing(function ($action) {
            return $action
                ->slideOver()
                ->iconButton();
        });

        Toggle::configureUsing(function (Toggle $component) {
            return $component
                ->inline(false);
        });
    }
}
