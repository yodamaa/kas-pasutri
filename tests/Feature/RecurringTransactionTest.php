<?php

namespace Tests\Feature;

use App\Models\RecurringTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesTenantData;
use Tests\TestCase;

class RecurringTransactionTest extends TestCase
{
    use CreatesTenantData, RefreshDatabase;

    private function makeRecurring(array $overrides = []): RecurringTransaction
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);

        return RecurringTransaction::create(array_merge([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'peruntukan_id' => $this->makeCategory('pengeluaran', $couple)->id,
            'metode_pembayaran_id' => $this->makePaymentMethod($couple)->id,
            'tipe' => 'pengeluaran',
            'jumlah' => 100_000,
            'deskripsi' => 'Tagihan rutin',
            'frequency' => 'daily',
            'starts_at' => '2026-09-01',
            'is_active' => true,
        ], $overrides));
    }

    public function test_harian_terjadi_setiap_tanggal(): void
    {
        $recurring = $this->makeRecurring();

        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-09-01')));
        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-09-14')));
        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-09-30')));
    }

    public function test_mingguan_terjadi_pada_hari_tertentu(): void
    {
        $recurring = $this->makeRecurring([
            'frequency' => 'weekly',
            'day_of_week' => 1, // Senin
        ]);

        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-09-14'))); // Senin
        $this->assertFalse($recurring->isOccurrenceDate(Carbon::parse('2026-09-13'))); // Minggu
        $this->assertFalse($recurring->isOccurrenceDate(Carbon::parse('2026-09-15'))); // Selasa
    }

    public function test_bulanan_terjadi_pada_tanggal_dengan_pembulatan_akhir_bulan(): void
    {
        $recurring = $this->makeRecurring([
            'frequency' => 'monthly',
            'day_of_month' => 31,
        ]);

        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-01-31')));
        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-02-28'))); // Februari 2026
        $this->assertFalse($recurring->isOccurrenceDate(Carbon::parse('2026-02-27')));
    }

    public function test_tahunan_terjadi_pada_bulan_dan_tanggal_tertentu(): void
    {
        $recurring = $this->makeRecurring([
            'frequency' => 'yearly',
            'month' => 6,
            'day_of_month' => 15,
        ]);

        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2026-06-15')));
        $this->assertTrue($recurring->isOccurrenceDate(Carbon::parse('2027-06-15')));
        $this->assertFalse($recurring->isOccurrenceDate(Carbon::parse('2026-06-16')));
        $this->assertFalse($recurring->isOccurrenceDate(Carbon::parse('2026-07-15')));
    }

    public function test_generate_for_missing_dates_membuat_transaksi(): void
    {
        $recurring = $this->makeRecurring([
            'frequency' => 'daily',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-09-03',
        ]);

        $created = $recurring->generateForMissingDates();

        $this->assertSame(3, $created);
        $this->assertDatabaseCount('transactions', 3);
        $this->assertSame('2026-09-03', $recurring->fresh()->last_generated_at?->toDateString());
        $this->assertTrue($recurring->generateForMissingDates() === 0);
    }

    public function test_generate_for_missing_dates_menghormati_tanggal_akhir(): void
    {
        $recurring = $this->makeRecurring([
            'frequency' => 'monthly',
            'day_of_month' => 10,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-09-09',
        ]);

        $this->assertSame(0, $recurring->generateForMissingDates());
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_command_menghasilkan_transaksi_berulang_yang_jatuh_tempo(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);

        RecurringTransaction::create([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'peruntukan_id' => $this->makeCategory('pengeluaran', $couple)->id,
            'metode_pembayaran_id' => $this->makePaymentMethod($couple)->id,
            'tipe' => 'pengeluaran',
            'jumlah' => 500_000,
            'deskripsi' => 'Gaji bulanan',
            'frequency' => 'monthly',
            'day_of_month' => now()->day,
            'starts_at' => now()->startOfMonth()->toDateString(),
            'is_active' => true,
        ]);

        $this->artisan('app:generate-recurring-transactions')
            ->assertSuccessful();

        $this->assertDatabaseHas('transactions', [
            'is_recurring' => true,
            'deskripsi' => 'Gaji bulanan',
        ]);
    }

    public function test_command_mengabaikan_recurring_tidak_aktif(): void
    {
        $this->makeRecurring([
            'deskripsi' => 'Non-aktif',
            'is_active' => false,
            'frequency' => 'daily',
            'starts_at' => '2026-09-01',
        ]);

        $this->artisan('app:generate-recurring-transactions')
            ->assertSuccessful();

        $this->assertDatabaseMissing('transactions', ['deskripsi' => 'Non-aktif']);
    }
}
