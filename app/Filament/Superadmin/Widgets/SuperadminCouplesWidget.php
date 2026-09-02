<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Couple;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class SuperadminCouplesWidget extends TableWidget
{
    protected static ?string $heading = 'Rekap per Pasangan';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public ?array $filters = null;

    private ?array $rekap = null;

    protected function getRekap(): array
    {
        if ($this->rekap !== null) {
            return $this->rekap;
        }

        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $rows = Transaction::query()
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw("couple_id, coalesce(sum(case when tipe = 'pemasukan' then jumlah else 0 end), 0) as pemasukan, coalesce(sum(case when tipe = 'pengeluaran' then jumlah else 0 end), 0) as pengeluaran")
            ->groupBy('couple_id')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row->couple_id] = [
                'pemasukan' => (float) $row->pemasukan,
                'pengeluaran' => (float) $row->pengeluaran,
                'saldo' => (float) $row->pemasukan - (float) $row->pengeluaran,
            ];
        }

        return $this->rekap = $result;
    }

    protected function rekapOf(Couple $couple): array
    {
        return $this->getRekap()[(int) $couple->getKey()] ?? ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
    }

    public function table(Table $table): Table
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $this->rekap = null;
        static::$heading = 'Rekap per Pasangan — '.Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');

        return $table
            ->query(Couple::query()->withCount('users'))
            ->columns([
                TextColumn::make('nama')
                    ->label('Pasangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->searchable(),
                TextColumn::make('users_count')
                    ->label('Anggota')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('pemasukan')
                    ->label('Pemasukan')
                    ->formatStateUsing(fn ($record) => 'Rp '.number_format($this->rekapOf($record)['pemasukan'], 0, ',', '.'))
                    ->color('success')
                    ->alignEnd(),
                TextColumn::make('pengeluaran')
                    ->label('Pengeluaran')
                    ->formatStateUsing(fn ($record) => 'Rp '.number_format($this->rekapOf($record)['pengeluaran'], 0, ',', '.'))
                    ->color('danger')
                    ->alignEnd(),
                TextColumn::make('saldo')
                    ->label('Saldo Bulan')
                    ->formatStateUsing(fn ($record) => 'Rp '.number_format($this->rekapOf($record)['saldo'], 0, ',', '.'))
                    ->color(fn ($record) => $this->rekapOf($record)['saldo'] >= 0 ? 'success' : 'danger')
                    ->weight('bold')
                    ->alignEnd(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('nama')
            ->recordActions([
                Action::make('buka_dashboard')
                    ->label('Buka Dashboard Pasangan')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn ($record) => url('/admin/'.$record->getKey()))
                    ->openUrlInNewTab(),
            ]);
    }
}
