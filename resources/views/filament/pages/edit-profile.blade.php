<x-filament-panels::page>
    <form wire:submit="updateProfile">
        {{ $this->editProfileForm }}

        <div class="mt-5">
            {{ $this->getUpdateProfileFormAction() }}
        </div>
    </form>

    <form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}

        <div class="mt-5">
            {{ $this->getUpdatePasswordFormAction() }}
        </div>
    </form>
</x-filament-panels::page>
