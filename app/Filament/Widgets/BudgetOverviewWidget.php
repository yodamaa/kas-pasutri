<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Transaction;
use Filament\Widgets\Widget;

class BudgetOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.budget-overview';

    public function getBudgets(): array
    {
        $now = now();
        $budgets = Budget::where('bulan', $now->month)
            ->where('tahun', $now->year)
            ->with('peruntukan')
            ->get();

        $result = [];
        foreach ($budgets as $budget) {
            $spent = Transaction::where('tipe', 'pengeluaran')
                ->where(function ($query) use ($budget) {
                    $query->where('budget_id', $budget->id)
                        ->orWhere(function ($q) use ($budget) {
                            $q->whereNull('budget_id')
                                ->where('peruntukan_id', $budget->peruntukan_id);
                        });
                })
                ->whereMonth('tanggal', $now->month)
                ->whereYear('tanggal', $now->year)
                ->sum('jumlah');

            $percentage = $budget->jumlah > 0 ? round(($spent / $budget->jumlah) * 100, 1) : 0;

            $result[] = [
                'nama' => $budget->peruntukan->nama ?? '-',
                'anggaran' => $budget->jumlah,
                'terpakai' => $spent,
                'persentase' => min($percentage, 100),
                'sisa' => max($budget->jumlah - $spent, 0),
            ];
        }

        return $result;
    }
}
