<?php

namespace App\Filament\Pages;

use App\Models\User;
use DateTime;
use DateTimeZone;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Illuminate\Contracts\Support\Htmlable;
use LogicException;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @property Schema $editProfileForm
 * @property Schema $editPasswordForm
 */
class EditProfile extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected string $view = 'filament.pages.edit-profile';

    protected static ?string $slug = 'profile';

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('filament-panels::auth/pages/edit-profile.label');
    }

    protected static bool $shouldRegisterNavigation = false;

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    public function editProfileForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('auth/pages/edit-profile.section.profile.label'))
                    ->description(__('auth/pages/edit-profile.section.profile.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-panels::auth/pages/edit-profile.form.name.label'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('identity_number')
                            ->label(__('auth/pages/edit-profile.form.identity_number.label'))
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText(__('auth/pages/edit-profile.form.identity_number.helper_text')),
                    ])
                    ->columnSpan([
                        'xl' => 2,
                        'lg' => 'full',
                    ]),
                Group::make([
                    Section::make([
                        Select::make('timezone')
                            ->label(__('auth/pages/edit-profile.form.timezone.label'))
                            ->options(function () {
                                $list = timezone_identifiers_list();

                                return array_combine($list, array_map([$this, 'formatTimezoneLabel'], $list));
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                $allTimezones = timezone_identifiers_list();
                                $filteredTimezones = array_filter(
                                    $allTimezones,
                                    fn($tz) => str_contains(strtolower($tz), strtolower($search))
                                );

                                $options = [];
                                foreach ($filteredTimezones as $tz) {
                                    $options[$tz] = $this->formatTimezoneLabel($tz);
                                }

                                return $options;
                            })
                            ->searchable(),
                    ]),
                    Section::make()
                        ->schema([
                            TextEntry::make('created_at')
                                ->label(__('auth/pages/edit-profile.form.infolist.created_at.label'))
                                ->state(fn (User $record): string => $record->created_at->locale(app()->getLocale())->isoFormat('L LTS')),
                            TextEntry::make('updated_at')
                                ->label(__('auth/pages/edit-profile.form.infolist.updated_at.label'))
                                ->state(fn (User $record): string => $record->updated_at->locale(app()->getLocale())->isoFormat('L LTS')),
                        ]),
                ])->columnSpan([
                    'xl' => 1,
                    'lg' => 'full',
                ]),
            ])
            ->columns(3)
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editPasswordForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('auth/pages/edit-profile.section.password.label'))
                    ->description(__('auth/pages/edit-profile.section.profile.description'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('filament-panels::auth/pages/edit-profile.form.current_password.label'))
                            ->password()
                            ->required()
                            ->currentPassword(),
                        TextInput::make('password')
                            ->label(__('filament-panels::auth/pages/edit-profile.form.password.label'))
                            ->password()
                            ->required()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->same('passwordConfirmation'),
                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::auth/pages/edit-profile.form.password_confirmation.label'))
                            ->password()
                            ->required()
                            ->dehydrated(false),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }

    public function updateProfile(): void
    {
        try {
            $data = $this->editProfileForm->getState();

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }

        $this->sendSuccessNotification();
    }

    public function updatePassword(): void
    {
        try {
            $data = $this->editPasswordForm->getState();

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }

        session()->forget('password_hash_' . Filament::getCurrentOrDefaultPanel()->getAuthGuard());
        Filament::auth()->login($this->getUser());

        $this->editPasswordForm->fill();

        $this->sendSuccessNotification();
    }

    public function getUpdateProfileFormAction(): Action
    {
        return Action::make('updateProfileAction')
            ->label(__('filament-panels::auth/pages/edit-profile.form.actions.save.label'))
            ->submit('editProfileForm');
    }

    public function getUpdatePasswordFormAction(): Action
    {
        return Action::make('updatePasswordAction')
            ->label(__('filament-panels::auth/pages/edit-profile.form.actions.save.label'))
            ->submit('editPasswordForm');
    }

    public function getUser(): Authenticatable|Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new LogicException('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title(__('filament-panels::auth/pages/edit-profile.notifications.saved.title'))
            ->send();
    }

    private function formatTimezoneLabel(string $timezoneIdentifier): string
    {
        try {
            $dateTime = new DateTime('now', new DateTimeZone($timezoneIdentifier));
            $offsetString = $dateTime->format('P');

            return "GMT{$offsetString} {$timezoneIdentifier}";
        } catch (Exception $e) {
            return $timezoneIdentifier;
        }
    }
}
