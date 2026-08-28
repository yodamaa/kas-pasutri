<?php

namespace App\Filament\Resources\RecurringTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecurringTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    }),
                TextColumn::make('peruntukan.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state, $record) => $record->tipe === 'pemasukan'
                        ? '+ Rp '.number_format($state, 0, ',', '.')
                        : '- Rp '.number_format($state, 0, ',', '.'))
                    ->color(fn ($record) => $record->tipe === 'pemasukan' ? 'success' : 'danger')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->formatStateUsing(fn ($state, $record) => $record->frequencyLabel())
                    ->badge()
                    ->color('info'),
                TextColumn::make('_day')
                    ->label('Jadwal')
                    ->getStateUsing(fn ($record) => $record->dayLabel())
                    ->placeholder('-'),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->placeholder('∞')
                    ->toggleable(),
                TextColumn::make('last_generated_at')
                    ->label('Terakhir Dibuat')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('is_active', 'desc')
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                SelectFilter::make('frequency')
                    ->label('Frekuensi')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'yearly' => 'Tahunan',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Nonaktif',
                    ]),
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
