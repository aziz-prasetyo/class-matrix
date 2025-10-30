<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect(Filament::getLoginUrl()));
