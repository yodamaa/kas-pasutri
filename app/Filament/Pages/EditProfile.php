<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use MatondoJK\FilamentAvatarPicker\Components\AvatarPicker;

class EditProfile extends BaseEditProfile
{
    protected static bool $shouldRegisterNavigation = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                AvatarPicker::make('avatar')
                    ->label('Foto Profil')
                    ->imagePreviewHeight(80),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}