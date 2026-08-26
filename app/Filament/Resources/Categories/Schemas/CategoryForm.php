<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->placeholder('Contoh: Makanan & Minuman')
                    ->required()
                    ->maxLength(255),
                Select::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->required(),
                TextInput::make('icon')
                    ->label('Icon (Emoji)')
                    ->placeholder('Contoh: 🍔'),
                ColorPicker::make('warna')
                    ->label('Warna')
                    ->default('#374151'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
