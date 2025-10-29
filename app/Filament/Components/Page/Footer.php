<?php

namespace App\Filament\Components\Page;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public function render(): View
    {
        return view('filament.components.page.footer');
    }
}
