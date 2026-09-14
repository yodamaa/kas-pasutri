<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTenantData;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use CreatesTenantData, RefreshDatabase;

    public function test_budget_terpakai_menghitung_pengeluaran_bulan_berjalan(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);
        $category = $this->makeCategory('pengeluaran', $couple);
        $budget = $this->makeBudget($couple, $category, 1_000_000);

        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $category->id,
            'jumlah' => 250_000,
            'tanggal' => now(),
        ]);

        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $category->id,
            'jumlah' => 150_000,
            'tanggal' => now(),
        ]);

        $this->assertSame(400_000.0, $budget->fresh()->terpakai);
        $this->assertSame(600_000.0, $budget->fresh()->sisa);
        $this->assertSame(40.0, $budget->fresh()->persentase);
    }

    public function test_transaksi_pasangan_lain_tidak_mempengaruhi_anggaran(): void
    {
        $coupleA = $this->makeCouple('Pasangan A', 'PA001');
        $coupleB = $this->makeCouple('Pasangan B', 'PB001');
        $userA = $this->makeUser('suami', $coupleA);

        $categoryA = $this->makeCategory('pengeluaran', $coupleA);
        $categoryB = $this->makeCategory('pengeluaran', $coupleB);
        $budget = $this->makeBudget($coupleA, $categoryA, 1_000_000);

        $this->makeTransaction($coupleA, $userA, [
            'peruntukan_id' => $categoryA->id,
            'jumlah' => 100_000,
            'tanggal' => now(),
        ]);

        $userB = $this->makeUser('istri', $coupleB);
        $this->makeTransaction($coupleB, $userB, [
            'peruntukan_id' => $categoryB->id,
            'jumlah' => 999_000,
            'tanggal' => now(),
        ]);

        $this->assertSame(100_000.0, $budget->fresh()->terpakai);
    }

    public function test_transaksi_soft_deleted_tidak_dihitung_dalam_anggaran(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);
        $category = $this->makeCategory('pengeluaran', $couple);
        $budget = $this->makeBudget($couple, $category, 1_000_000);

        $transaction = $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $category->id,
            'jumlah' => 500_000,
            'tanggal' => now(),
        ]);

        $this->assertSame(500_000.0, $budget->fresh()->terpakai);

        $transaction->delete();

        $this->assertNotNull($transaction->fresh()->deleted_at);
        $this->assertSame(0.0, $budget->fresh()->terpakai);
    }

    public function test_saldo_pemasukan_dan_pengeluaran_per_bulan(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);
        $pemasukan = $this->makeCategory('pemasukan', $couple);
        $pengeluaran = $this->makeCategory('pengeluaran', $couple);

        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $pemasukan->id,
            'tipe' => 'pemasukan',
            'jumlah' => 2_000_000,
            'tanggal' => now(),
        ]);
        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $pengeluaran->id,
            'jumlah' => 750_000,
            'tanggal' => now(),
        ]);

        $pemasukanTotal = Transaction::where('couple_id', $couple->id)
            ->where('tipe', 'pemasukan')
            ->sum('jumlah');
        $pengeluaranTotal = Transaction::where('couple_id', $couple->id)
            ->where('tipe', 'pengeluaran')
            ->sum('jumlah');

        $this->assertSame(2_000_000.0, (float) $pemasukanTotal);
        $this->assertSame(750_000.0, (float) $pengeluaranTotal);
    }
}
