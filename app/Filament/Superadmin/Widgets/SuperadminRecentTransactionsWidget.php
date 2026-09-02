<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class SuperadminRecentTransactionsWidget extends TableWidget
{
    protected static ?string $heading = 'Transaksi Terakhir (Semua Pasangan)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public ?array $filters = null;

    public function table(Table $table): Table
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        return $table
            ->query(Transaction::with(['peruntukan', 'user', 'couple'])
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->latest('tanggal'))
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('couple.nama')
                    ->label('Pasangan')
                    ->searchable(),
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
                        ? '+ Rp '.number_format($state, 0, ',', '.')
                        : '- Rp '.number_format($state, 0, ',', '.'))
                    ->color(fn ($record) => $record->tipe === 'pemasukan' ? 'success' : 'danger')
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->searchable(),
            ])
            ->paginated([5]);
    }
}
