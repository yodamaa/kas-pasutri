<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        User::create([
            'name' => 'Superadmin',
            'email' => 'admin@email.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        // Kategori Pengeluaran (global)
        $pengeluaran = [
            ['nama' => 'Makanan & Minuman', 'icon' => '🍔', 'warna' => '#ef4444'],
            ['nama' => 'Transportasi', 'icon' => '🚗', 'warna' => '#f97316'],
            ['nama' => 'Tagihan Listrik', 'icon' => '💡', 'warna' => '#eab308'],
            ['nama' => 'Tagihan Air', 'icon' => '💧', 'warna' => '#06b6d4'],
            ['nama' => 'Internet & Pulsa', 'icon' => '📱', 'warna' => '#3b82f6'],
            ['nama' => 'Kesehatan', 'icon' => '🏥', 'warna' => '#22c55e'],
            ['nama' => 'Pendidikan', 'icon' => '📚', 'warna' => '#8b5cf6'],
            ['nama' => 'Pakaian', 'icon' => '👕', 'warna' => '#ec4899'],
            ['nama' => 'Rumah Tangga', 'icon' => '🏠', 'warna' => '#14b8a6'],
            ['nama' => 'Hiburan', 'icon' => '🎬', 'warna' => '#a855f7'],
            ['nama' => 'Sedekah', 'icon' => '🤲', 'warna' => '#f43f5e'],
            ['nama' => 'Lainnya (Pengeluaran)', 'icon' => '📦', 'warna' => '#6366f1'],
        ];

        foreach ($pengeluaran as $item) {
            Category::create([...$item, 'tipe' => 'pengeluaran']);
        }

        // Kategori Pemasukan (global)
        $pemasukan = [
            ['nama' => 'Gaji', 'icon' => '💰', 'warna' => '#10b981'],
            ['nama' => 'Bonus', 'icon' => '🎁', 'warna' => '#059669'],
            ['nama' => 'Hasil Usaha', 'icon' => '💼', 'warna' => '#047857'],
            ['nama' => 'Investasi', 'icon' => '📈', 'warna' => '#065f46'],
            ['nama' => 'Transfer', 'icon' => '🔄', 'warna' => '#064e3b'],
            ['nama' => 'Lainnya (Pemasukan)', 'icon' => '💵', 'warna' => '#374151'],
        ];

        foreach ($pemasukan as $item) {
            Category::create([...$item, 'tipe' => 'pemasukan']);
        }

        // Metode Pembayaran (global)
        $paymentMethods = [
            ['nama' => 'Tunai', 'icon' => '💵', 'warna' => '#10b981'],
            ['nama' => 'Transfer BCA', 'icon' => '🏦', 'warna' => '#2563eb'],
            ['nama' => 'Transfer Mandiri', 'icon' => '🏦', 'warna' => '#f97316'],
            ['nama' => 'Transfer BRI', 'icon' => '🏦', 'warna' => '#dc2626'],
            ['nama' => 'Transfer BNI', 'icon' => '🏦', 'warna' => '#ef4444'],
            ['nama' => 'GoPay', 'icon' => '💚', 'warna' => '#22c55e'],
            ['nama' => 'OVO', 'icon' => '💜', 'warna' => '#7c3aed'],
            ['nama' => 'Dana', 'icon' => '💙', 'warna' => '#3b82f6'],
            ['nama' => 'Kartu Kredit', 'icon' => '💳', 'warna' => '#eab308'],
            ['nama' => 'Lainnya', 'icon' => '🔖', 'warna' => '#6b7280'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
