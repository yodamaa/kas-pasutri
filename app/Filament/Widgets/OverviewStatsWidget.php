<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OverviewStatsWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    public ?array $filters = null;

    protected function getStats(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $totalPemasukan = Transaction::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $totalPengeluaran = Transaction::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $saldo = $totalPemasukan - $totalPengeluaran;

        $jumlahTransaksi = Transaction::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count();

        $label = Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Bulan ' . $label)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'))
                ->description('Bulan ' . $label)
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([5, 8, 3, 6, 4, 7, 3, 6]),

            Stat::make('Saldo Bersih', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description($saldo >= 0 ? 'Surplus' : 'Defisit')
                ->descriptionIcon($saldo >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($saldo >= 0 ? 'success' : 'danger'),

            Stat::make('Jumlah Transaksi', $jumlahTransaksi)
                ->description('Transaksi bulan ' . $label)
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
