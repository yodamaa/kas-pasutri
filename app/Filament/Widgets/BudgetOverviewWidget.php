<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use Filament\Widgets\Widget;

class BudgetOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.budget-overview';

    public ?array $filters = null;

    public function getBudgets(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        $budgets = Budget::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('peruntukan')
            ->get();

        $result = [];
        foreach ($budgets as $budget) {
            $result[] = [
                'nama' => $budget->peruntukan->nama ?? '-',
                'anggaran' => $budget->jumlah,
                'terpakai' => $budget->terpakai,
                'persentase' => $budget->persentase,
                'sisa' => $budget->sisa,
            ];
        }

        return $result;
    }
}
