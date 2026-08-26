<?php

namespace App\Filament\Resources\Budgets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->columns([
                TextColumn::make('peruntukan.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Anggaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn ($state) => $bulanNames[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
