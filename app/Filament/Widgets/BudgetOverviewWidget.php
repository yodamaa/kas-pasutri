<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class BudgetOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.budget-overview';

    public ?array $filters = null;

    public function getBudgets(): array
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        return self::collectBudgets($this->resolveCoupleId(), $bulan, $tahun);
    }

    public function getMonthLabel(): string
    {
        $bulan = $this->filters['bulan'] ?? now()->month;
        $tahun = $this->filters['tahun'] ?? now()->year;

        return Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');
    }

    public static function collectBudgets(?int $coupleId, int $bulan, int $tahun): array
    {
        $budgets = Budget::query()
            ->with('peruntukan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($coupleId !== null, fn ($query) => $query->where('couple_id', $coupleId))
            ->get();

        return $budgets
            ->map(fn (Budget $budget): array => [
                'nama' => $budget->nama ?: ($budget->peruntukan->nama ?? '-'),
                'anggaran' => (float) $budget->jumlah,
                'terpakai' => $budget->terpakai,
                'persentase' => $budget->persentase,
                'sisa' => $budget->sisa,
            ])
            ->all();
    }

    private function resolveCoupleId(): ?int
    {
        try {
            return Filament::getTenant()?->getKey();
        } catch (\Throwable) {
            return null;
        }
    }
}
