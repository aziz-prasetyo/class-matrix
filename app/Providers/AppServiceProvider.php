<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentColor;
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
        FilamentColor::register([
            'primary' => [
                50 => 'oklch(96.67% 0.016 22.18)',
                100 => 'oklch(94.24% 0.029 23.01)',
                200 => 'oklch(88.66% 0.06 23.47)',
                300 => 'oklch(82.91% 0.095 24.08)',
                400 => 'oklch(77.33% 0.133 26.67)',
                500 => 'oklch(71.88% 0.176 29.79)',
                600 => 'oklch(66.02% 0.229 35.4)',
                700 => 'oklch(52.92% 0.184 35.42)',
                800 => 'oklch(40.3% 0.14 35.49)',
                900 => 'oklch(27% 0.094 35.32)',
                950 => 'oklch(20.67% 0.072 35.38)',
            ],
        ]);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','id'])
                ->visible(outsidePanels: true)
                ->outsidePanelPlacement(Placement::TopRight);
        });
    }
}
