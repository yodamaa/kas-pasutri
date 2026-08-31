<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyComparisonChart extends ChartWidget
{
    protected ?string $heading = 'Pemasukan vs Pengeluaran';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    public ?array $filters = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $base = Carbon::create($tahun, $bulan, 1);

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = $base->copy()->subMonths($i);
            $months->push([
                'month' => $month->translatedFormat('M Y'),
                'start' => $month->copy()->startOfMonth(),
                'end' => $month->copy()->endOfMonth(),
            ]);
        }

        $this->heading = 'Pemasukan vs Pengeluaran (6 Bulan hingga ' . $base->translatedFormat('F Y') . ')';

        $byKey = Transaction::query()
            ->selectRaw('tipe, year(tanggal) as th, month(tanggal) as bl, sum(jumlah) as total')
            ->whereBetween('tanggal', [$months->first()['start'], $months->last()['end']])
            ->groupBy('tipe', 'th', 'bl')
            ->get()
            ->keyBy(fn ($row) => $row->tipe . ':' . $row->th . '-' . $row->bl);

        $pemasukan = $months->map(fn ($m) => (float) ($byKey['pemasukan:' . $m['start']->format('Y-n')]->total ?? 0))->toArray();

        $pengeluaran = $months->map(fn ($m) => (float) ($byKey['pengeluaran:' . $m['start']->format('Y-n')]->total ?? 0))->toArray();

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
