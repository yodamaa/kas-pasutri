<?php

namespace App\Filament\Resources\Budgets\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('peruntukan_id')
                    ->label('Jenis Peruntukan')
                    ->relationship('peruntukan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('jumlah')
                    ->label('Anggaran (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->inputMode('numeric')
                    ->step(10000),
                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->default((int) now()->format('m'))
                    ->required(),
                TextInput::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->numeric()
                    ->default((int) now()->format('Y'))
                    ->minLength(4)
                    ->maxLength(4),
            ]);
    }
}
