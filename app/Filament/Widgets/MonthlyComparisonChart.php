<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyComparisonChart extends ChartWidget
{
    protected ?string $heading = 'Pemasukan vs Pengeluaran (6 Bulan Terakhir)';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $month->translatedFormat('M Y'),
                'start' => $month->copy()->startOfMonth(),
                'end' => $month->copy()->endOfMonth(),
            ]);
        }

        $pemasukan = $months->map(fn ($m) => Transaction::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$m['start'], $m['end']])->sum('jumlah'))->toArray();

        $pengeluaran = $months->map(fn ($m) => Transaction::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$m['start'], $m['end']])->sum('jumlah'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $pemasukan,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaran,
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }
}
