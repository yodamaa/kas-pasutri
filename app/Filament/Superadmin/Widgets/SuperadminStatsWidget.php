<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Couple;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SuperadminStatsWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    public ?array $filters = null;

    protected function getStats(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $row = Transaction::query()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw("coalesce(sum(case when tipe = 'pemasukan' then jumlah else 0 end), 0) as pemasukan, coalesce(sum(case when tipe = 'pengeluaran' then jumlah else 0 end), 0) as pengeluaran, count(*) as total")
            ->first();

        $totalPemasukan = (float) $row->pemasukan;
        $totalPengeluaran = (float) $row->pengeluaran;

        $saldo = $totalPemasukan - $totalPengeluaran;

        $label = Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');

        return [
            Stat::make('Pasangan Aktif', Couple::where('is_active', true)->count())
                ->description('Dari total '.Couple::count().' pasangan')
                ->descriptionIcon('heroicon-m-heart')
                ->color('primary'),

            Stat::make('Total User', User::count())
                ->description('Superadmin, suami, istri')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Total Pemasukan (Semua)', 'Rp '.number_format($totalPemasukan, 0, ',', '.'))
                ->description('Bulan '.$label)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Pengeluaran (Semua)', 'Rp '.number_format($totalPengeluaran, 0, ',', '.'))
                ->description('Bulan '.$label)
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Saldo Bersih (Semua)', 'Rp '.number_format($saldo, 0, ',', '.'))
                ->description($saldo >= 0 ? 'Surplus gabungan' : 'Defisit gabungan')
                ->descriptionIcon($saldo >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($saldo >= 0 ? 'success' : 'danger'),

            Stat::make('Jumlah Transaksi', (int) $row->total)
                ->description('Transaksi bulan '.$label)
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
