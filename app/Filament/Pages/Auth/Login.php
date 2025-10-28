<?php

namespace App\Filament\Pages\Auth;

use Database\Factories\UserFactory;
use Filament\Auth\Pages\Login as BasePage;

class Login extends BasePage
{
    protected string $view = 'filament.pages.auth.login';

    public function mount(): void
    {
        parent::mount();

        if (app()->isLocal()) {
            $this->form->fill([
                'email' => '2110511095@mahasiswa.upnvj.ac.id',
                'password' => UserFactory::$password,
                'remember' => true,
            ]);
        }
    }
}
