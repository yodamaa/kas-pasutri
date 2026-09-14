<?php

namespace Tests\Feature;

use App\Exports\MonthlyReportExport;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\CreatesTenantData;
use Tests\TestCase;

class MonthlyReportExportTest extends TestCase
{
    use CreatesTenantData, RefreshDatabase;

    private function exportPdf(int $coupleId, int $bulan, int $tahun): TestResponse
    {
        $response = app(MonthlyReportExport::class)->download($coupleId, $bulan, $tahun);

        return TestResponse::fromBaseResponse($response);
    }

    public function test_export_pdf_mengembalikan_stream_konten_pdf(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);
        $category = $this->makeCategory('pengeluaran', $couple);
        $this->makeBudget($couple, $category, 1_000_000);

        $this->makeTransaction($couple, $user, [
            'peruntukan_id' => $category->id,
            'jumlah' => 200_000,
            'tanggal' => now(),
        ]);

        $response = $this->exportPdf($couple->id, now()->month, now()->year);

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('content-type') ?? '',
        );
        $this->assertStringContainsString(
            'laporan-'.now()->month.'-'.now()->year.'.pdf',
            $response->headers->get('content-disposition') ?? '',
        );
        $this->assertStringStartsWith('%PDF', $response->streamedContent());
    }

    public function test_export_pdf_hanya_memuat_transaksi_pasangan_terkait(): void
    {
        $coupleA = $this->makeCouple('Pasangan A', 'PA001');
        $coupleB = $this->makeCouple('Pasangan B', 'PB001');
        $userA = $this->makeUser('suami', $coupleA, 'User A');
        $userB = $this->makeUser('istri', $coupleB, 'User B');

        $categoryA = $this->makeCategory('pengeluaran', $coupleA);
        $this->makeCategory('pengeluaran', $coupleB);

        $this->makeTransaction($coupleA, $userA, [
            'peruntukan_id' => $categoryA->id,
            'jumlah' => 250_000,
            'tanggal' => now(),
        ]);
        $this->makeTransaction($coupleB, $userB, [
            'peruntukan_id' => $this->makeCategory('pemasukan', $coupleB)->id,
            'tipe' => 'pemasukan',
            'jumlah' => 9_000_000,
            'tanggal' => now(),
        ]);

        $response = $this->exportPdf($coupleA->id, now()->month, now()->year);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringStartsWith('%PDF', $response->streamedContent());
        $this->assertDatabaseHas('transactions', ['couple_id' => $coupleA->id, 'jumlah' => 250_000]);
        $this->assertDatabaseHas('transactions', ['couple_id' => $coupleB->id, 'jumlah' => 9_000_000]);
        $this->assertDatabaseMissing('transactions', ['couple_id' => $coupleA->id, 'jumlah' => 9_000_000]);
    }

    public function test_export_pdf_hanya_memuat_anggaran_bulan_dan_pasangan_terkait(): void
    {
        $coupleA = $this->makeCouple('Pasangan A', 'PA001');
        $coupleB = $this->makeCouple('Pasangan B', 'PB001');
        $categoryA = $this->makeCategory('pengeluaran', $coupleA);
        $categoryB = $this->makeCategory('pengeluaran', $coupleB);

        $this->makeBudget($coupleA, $categoryA, 1_000_000, 1, 2026);
        $this->makeBudget($coupleA, $categoryA, 2_000_000, 2, 2026);
        $this->makeBudget($coupleB, $categoryB, 5_000_000, 1, 2026);

        $response = $this->exportPdf($coupleA->id, 1, 2026);

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->streamedContent());
        $this->assertSame(1_000_000.0, (float) Budget::where('couple_id', $coupleA->id)
            ->where('bulan', 1)
            ->where('tahun', 2026)
            ->sum('jumlah'));
    }
}
