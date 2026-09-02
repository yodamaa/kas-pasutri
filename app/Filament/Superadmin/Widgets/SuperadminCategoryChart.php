<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SuperadminCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Pengeluaran per Kategori (Semua Pasangan)';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    public ?array $filters = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $this->heading = 'Pengeluaran per Kategori Semua Pasangan ('.Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y').')';

        $data = Transaction::where('transactions.tipe', 'pengeluaran')
            ->whereBetween('transactions.tanggal', [$startOfMonth, $endOfMonth])
            ->join('categories', 'transactions.peruntukan_id', '=', 'categories.id')
            ->selectRaw('categories.nama, sum(transactions.jumlah) as total')
            ->groupBy('categories.nama')
            ->pluck('total', 'nama')
            ->toArray();

        $colors = [
            '#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4',
            '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f43f5e',
            '#a855f7', '#6366f1',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }
}
