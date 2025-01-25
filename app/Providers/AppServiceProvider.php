<?php

namespace App\Providers;

use App\Models\Chat;
use App\Observers\ChatObserver;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
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
        //Observers
        Chat::observe(ChatObserver::class);
        //Dashboard Language Switch Package
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar','en']);
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label(__('Add Data'))
                    ->icon('heroicon-o-bars-arrow-up'),
                NavigationGroup::make()
                    ->label(__('Accounts'))
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label(__('Reservations'))
                    ->icon('heroicon-o-calendar-days'),
                NavigationGroup::make()
                      ->label(__('Financial'))
                      ->icon('heroicon-o-banknotes'),
                      NavigationGroup::make()
                      ->label(__('Admins'))
                      ->icon('heroicon-o-shield-check'),
                NavigationGroup::make()
                      ->label(__('Configuration'))
                       ->icon('heroicon-o-cog-8-tooth'),
            ]);
        });
    }
}
