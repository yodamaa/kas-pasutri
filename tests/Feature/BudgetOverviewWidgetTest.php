<?php

namespace Tests\Feature;

use App\Filament\Widgets\BudgetOverviewWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenantData;
use Tests\TestCase;

class BudgetOverviewWidgetTest extends TestCase
{
    use CreatesTenantData, RefreshDatabase;

    public function test_collect_budgets_hanya_memuat_anggaran_pasangan_terkait(): void
    {
        $coupleA = $this->makeCouple('Pasangan A', 'PA001');
        $coupleB = $this->makeCouple('Pasangan B', 'PB001');
        $categoryA = $this->makeCategory('pengeluaran', $coupleA);
        $categoryB = $this->makeCategory('pengeluaran', $coupleB);

        $this->makeBudget($coupleA, $categoryA, 1_000_000, 9, 2026, 'Belanja');
        $this->makeBudget($coupleB, $categoryB, 5_000_000, 9, 2026);

        $budgets = BudgetOverviewWidget::collectBudgets($coupleA->id, 9, 2026);

        $this->assertCount(1, $budgets);
        $this->assertSame('Belanja', $budgets[0]['nama']);
        $this->assertSame(1_000_000.0, $budgets[0]['anggaran']);
    }

    public function test_collect_budgets_memfilter_bulan_dan_tahun(): void
    {
        $couple = $this->makeCouple();
        $category = $this->makeCategory('pengeluaran', $couple);

        $this->makeBudget($couple, $category, 1_000_000, 9, 2026);
        $this->makeBudget($couple, $category, 2_000_000, 10, 2026);
        $this->makeBudget($couple, $category, 3_000_000, 9, 2025);

        $budgets = BudgetOverviewWidget::collectBudgets($couple->id, 9, 2026);

        $this->assertCount(1, $budgets);
        $this->assertSame(1_000_000.0, $budgets[0]['anggaran']);
    }

    public function test_collect_budgets_menggunakan_nama_kategori_saat_tanpa_nama(): void
    {
        $couple = $this->makeCouple();
        $category = $this->makeCategory('pengeluaran', $couple, 'Makanan');
        $this->makeBudget($couple, $category, 500_000, 9, 2026);

        $budgets = BudgetOverviewWidget::collectBudgets($couple->id, 9, 2026);

        $this->assertSame('Makanan', $budgets[0]['nama']);
    }

    public function test_koleksi_mengembalikan_persentase_dan_terpakai(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);
        $category = $this->makeCategory('pengeluaran', $couple);
        $budget = $this->makeBudget($couple, $category, 1_000_000, 9, 2026);

        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $category->id,
            'jumlah' => 250_000,
            'tanggal' => '2026-09-10',
        ]);

        $budgets = BudgetOverviewWidget::collectBudgets($couple->id, 9, 2026);

        $this->assertSame(250_000.0, $budgets[0]['terpakai']);
        $this->assertSame(25.0, $budgets[0]['persentase']);
        $this->assertSame($budget->sisa, $budgets[0]['sisa']);
    }
}
