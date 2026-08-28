<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([
                \Filament\Forms\Components\Select::make('bulan')
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
                    ->default(now()->month)
                    ->required()
                    ->native(false),

                \Filament\Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->options(fn (): array => collect(range(now()->year - 2, now()->year + 1))
                        ->mapWithKeys(fn ($year) => [$year => (string) $year])
                        ->toArray())
                    ->default(now()->year)
                    ->required()
                    ->native(false),
            ]);
    }

    public function getWidgetData(): array
    {
        return [
            'filters' => $this->filters,
        ];
    }
}