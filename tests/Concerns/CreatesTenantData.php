<?php

namespace Tests\Concerns;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Couple;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;

trait CreatesTenantData
{
    protected function makeCouple(string $nama = 'Test Couple', string $kode = 'TC001'): Couple
    {
        return Couple::create([
            'nama' => $nama,
            'kode' => $kode,
            'is_active' => true,
        ]);
    }

    protected function makeUser(string $role, ?Couple $couple, string $name = 'Member'): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower($role).'.'.uniqid().'@test.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
            'couple_id' => $couple?->id,
        ]);
    }

    protected function makeCategory(string $tipe, ?Couple $couple = null, string $nama = 'Kategori'): Category
    {
        return Category::create([
            'nama' => $nama,
            'tipe' => $tipe,
            'icon' => '🏷️',
            'warna' => '#000000',
            'is_active' => true,
            'couple_id' => $couple?->id,
        ]);
    }

    protected function makePaymentMethod(?Couple $couple = null, string $nama = 'Tunai'): PaymentMethod
    {
        return PaymentMethod::create([
            'nama' => $nama,
            'icon' => '💵',
            'warna' => '#10b981',
            'is_active' => true,
            'couple_id' => $couple?->id,
        ]);
    }

    protected function makeBudget(
        Couple $couple,
        Category $peruntukan,
        float $jumlah = 1_000_000,
        ?int $bulan = null,
        ?int $tahun = null,
        ?string $nama = null,
        ?float $alertThreshold = null,
    ): Budget {
        $data = [
            'couple_id' => $couple->id,
            'peruntukan_id' => $peruntukan->id,
            'nama' => $nama,
            'jumlah' => $jumlah,
            'bulan' => $bulan ?? now()->month,
            'tahun' => $tahun ?? now()->year,
        ];

        if ($alertThreshold !== null) {
            $data['alert_threshold'] = $alertThreshold;
        }

        return Budget::create($data);
    }

    protected function makeTransaction(Couple $couple, User $user, array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'couple_id' => $couple->id,
            'user_id' => $user->id,
            'peruntukan_id' => $this->makeCategory('pengeluaran', $couple)->id,
            'metode_pembayaran_id' => $this->makePaymentMethod($couple)->id,
            'tipe' => 'pengeluaran',
            'jumlah' => 100_000,
            'tanggal' => now(),
            'deskripsi' => 'Transaksi test',
        ], $overrides));
    }
}
