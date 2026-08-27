<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Couple;
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

        // Pasangan 1
        $couple1 = Couple::create([
            'nama' => 'Ahmad & Fatimah',
            'kode' => 'PAS001',
        ]);

        $suami1 = User::create([
            'name' => 'Ahmad',
            'email' => 'ahmad@email.com',
            'password' => Hash::make('password'),
            'role' => 'suami',
            'couple_id' => $couple1->id,
        ]);

        User::create([
            'name' => 'Fatimah',
            'email' => 'fatimah@email.com',
            'password' => Hash::make('password'),
            'role' => 'istri',
            'couple_id' => $couple1->id,
        ]);

        // Pasangan 2
        $couple2 = Couple::create([
            'nama' => 'Budi & Citra',
            'kode' => 'PAS002',
        ]);

        $suami2 = User::create([
            'name' => 'Budi',
            'email' => 'budi@email.com',
            'password' => Hash::make('password'),
            'role' => 'suami',
            'couple_id' => $couple2->id,
        ]);

        User::create([
            'name' => 'Citra',
            'email' => 'citra@email.com',
            'password' => Hash::make('password'),
            'role' => 'istri',
            'couple_id' => $couple2->id,
        ]);

        // Kategori Pengeluaran - untuk kedua pasangan
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

        foreach ([$couple1, $couple2] as $couple) {
            foreach ($pengeluaran as $item) {
                Category::create([...$item, 'tipe' => 'pengeluaran', 'couple_id' => $couple->id]);
            }

            $pemasukan = [
                ['nama' => 'Gaji', 'icon' => '💰', 'warna' => '#10b981'],
                ['nama' => 'Bonus', 'icon' => '🎁', 'warna' => '#059669'],
                ['nama' => 'Hasil Usaha', 'icon' => '💼', 'warna' => '#047857'],
                ['nama' => 'Investasi', 'icon' => '📈', 'warna' => '#065f46'],
                ['nama' => 'Transfer', 'icon' => '🔄', 'warna' => '#064e3b'],
                ['nama' => 'Lainnya (Pemasukan)', 'icon' => '💵', 'warna' => '#374151'],
            ];

            foreach ($pemasukan as $item) {
                Category::create([...$item, 'tipe' => 'pemasukan', 'couple_id' => $couple->id]);
            }

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
                PaymentMethod::create([...$method, 'couple_id' => $couple->id]);
            }
        }
    }
}
