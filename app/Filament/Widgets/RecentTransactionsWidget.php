<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentTransactionsWidget extends TableWidget
{
    protected static ?string $heading = 'Transaksi Terakhir';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::with(['peruntukan', 'metodePembayaran', 'user'])->latest('tanggal'))
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    }),
                TextColumn::make('peruntukan.nama')
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->formatStateUsing(fn ($state, $record) => $record->tipe === 'pemasukan'
                        ? '+ Rp ' . number_format($state, 0, ',', '.')
                        : '- Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn ($record) => $record->tipe === 'pemasukan' ? 'success' : 'danger')
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->searchable(),
            ])
            ->paginated([5]);
    }
}
