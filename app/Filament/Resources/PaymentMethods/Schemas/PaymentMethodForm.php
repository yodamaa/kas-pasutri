<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Metode Pembayaran')
                    ->placeholder('Contoh: Transfer BCA')
                    ->required()
                    ->maxLength(255),
                TextInput::make('icon')
                    ->label('Icon (Emoji)')
                    ->placeholder('Contoh: 💳'),
                ColorPicker::make('warna')
                    ->label('Warna')
                    ->default('#374151'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
