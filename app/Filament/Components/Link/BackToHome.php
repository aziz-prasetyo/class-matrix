<?php

namespace App\Filament\Components\Link;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BackToHome extends Component
{
    public function render(): View
    {
        return view('filament.components.link.back-to-home');
    }
}
