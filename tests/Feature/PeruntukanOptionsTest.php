<?php

namespace Tests\Feature;

use App\Filament\Support\PeruntukanOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\CreatesTenantData;
use Tests\TestCase;

class PeruntukanOptionsTest extends TestCase
{
    use CreatesTenantData, RefreshDatabase;

    public function test_options_difilter_berdasarkan_tipe(): void
    {
        $pemasukan = $this->makeCategory('pemasukan', null, 'Gaji');
        $pengeluaran = $this->makeCategory('pengeluaran', null, 'Makanan');

        $optionsPemasukan = PeruntukanOptions::for('pemasukan');
        $optionsPengeluaran = PeruntukanOptions::for('pengeluaran');

        $this->assertSame('Gaji', $optionsPemasukan[$pemasukan->id]);
        $this->assertSame('Makanan', $optionsPengeluaran[$pengeluaran->id]);
    }

    public function test_opsi_mengecualikan_kategori_tidak_aktif(): void
    {
        $this->makeCategory('pengeluaran', null, 'Aktif');
        $this->makeCategory('pengeluaran', null, 'Nonaktif')
            ->update(['is_active' => false]);

        $this->assertSame(['Aktif'], $this->getAllNames(PeruntukanOptions::for('pengeluaran')));
    }

    public function test_cache_diinvalidasi_saat_kategori_dibuat(): void
    {
        Cache::flush();

        $this->makeCategory('pengeluaran', null, 'Lama');
        $this->assertSame(['Lama'], $this->getAllNames(PeruntukanOptions::for('pengeluaran')));

        $this->makeCategory('pengeluaran', null, 'Baru');

        $this->assertSame(['Baru', 'Lama'], $this->getAllNames(PeruntukanOptions::for('pengeluaran')));
    }

    public function test_cache_diinvalidasi_saat_kategori_diupdate(): void
    {
        Cache::flush();

        $category = $this->makeCategory('pengeluaran', null, 'Awal');
        $this->assertSame(['Awal'], $this->getAllNames(PeruntukanOptions::for('pengeluaran')));

        $category->update(['nama' => 'Ganti']);

        $this->assertSame(['Ganti'], $this->getAllNames(PeruntukanOptions::for('pengeluaran')));
    }

    private function getAllNames(array $options): array
    {
        return array_values(array_unique($options));
    }
}
