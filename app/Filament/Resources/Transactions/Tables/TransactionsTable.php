<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    }),
                TextColumn::make('peruntukan.nama')
                    ->label('Peruntukan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state, $record) => $record->tipe === 'pemasukan'
                        ? '+ ' . number_format($state, 0, ',', '.')
                        : '- ' . number_format($state, 0, ',', '.'))
                    ->color(fn ($record) => $record->tipe === 'pemasukan' ? 'success' : 'danger')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('metodePembayaran.nama')
                    ->label('Metode Bayar')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Oleh')
                    ->searchable(),
                IconColumn::make('is_recurring')
                    ->label('Berulang')
                    ->boolean(),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(30)
                    ->searchable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                SelectFilter::make('peruntukan_id')
                    ->label('Peruntukan')
                    ->options(fn () => Category::pluck('nama', 'id')),
                SelectFilter::make('metode_pembayaran_id')
                    ->label('Metode Pembayaran')
                    ->options(fn () => PaymentMethod::pluck('nama', 'id')),
                SelectFilter::make('user_id')
                    ->label('Dicatat oleh')
                    ->options(fn () => User::pluck('name', 'id')),
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
