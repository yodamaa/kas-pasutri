<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterCouple extends RegisterTenant
{
    protected static bool $isDiscovered = false;

    protected static ?string $slug = 'pasangan-baru';

    public static function getLabel(): string
    {
        return 'Buat Pasangan';
    }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'superadmin';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pasangan')
                    ->placeholder('Contoh: Ahmad & Fatimah')
                    ->required()
                    ->maxLength(255),
                TextInput::make('kode')
                    ->label('Kode')
                    ->placeholder('Contoh: PAS001')
                    ->required()
                    ->unique('couples', 'kode')
                    ->maxLength(50),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}