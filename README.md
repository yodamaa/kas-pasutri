# Uang Pasutri

Aplikasi web manajemen keuangan keluarga untuk pasangan suami istri. Mencatat, mengelola, dan memantau keuangan bersama secara transparan — dibangun dengan **Laravel 12** dan **Filament v5**.

## Fitur

- **Multi-pasangan (tenancy)** — tiap pasangan punya ruang data sendiri; superadmin memantau semua pasangan.
- **Panel Superadmin** (`/superadmin`) — dashboard global & rekap per pasangan, kelola pasangan, user, dan log aktivitas.
- **Dashboard** — statistik pemasukan/pengeluaran/saldo, grafik 6 bulan, pengeluaran per kategori, progress anggaran, transaksi terakhir; bisa difilter per bulan/tahun.
- **Transaksi** — CRUD lengkap dengan lampiran foto, filter, export Excel, **laporan bulanan PDF**, **import CSV**, dan **soft delete** (recycle bin).
- **Transaksi Berulang** — harian/mingguan/bulanan/tahunan, di-generate otomatis via scheduler.
- **Anggaran** — beberapa anggaran per kategori, progress bar, notifikasi saat melewati ambang batas.
- **Master data** — jenis pembayaran & jenis peruntukan (kategori).
- **Profil & avatar** — foto profil dari galeri maupun unggahan.
- **Audit log** — riwayat perubahan data otomatis.

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 12 (PHP 8.2+) |
| Admin Panel | Filament v5 |
| Database | MySQL 8.x (Laragon) atau SQLite (testing) |
| Export | DomPDF (PDF), Maatwebsite Excel (XLSX/CSV) |
| Avatar | matondojk/filament-avatar-picker |
| Charts | Filament ChartWidget (ApexCharts) |

## Persyaratan

- PHP ^8.2, Composer, Node.js & npm (untuk asset)
- MySQL (via Laragon) atau SQLite
- Ekstensi PHP yang biasa dibutuhkan Laravel (`fileinfo`, `gd`/`imagick` untuk PDF & gambar)

## Instalasi

```bash
composer install
cp .env.example .env          # sesuaikan kredensial DB
php artisan key:generate
php artisan migrate --seed
npm install && npm run build  # asset frontend
php artisan serve
```

Buka **http://127.0.0.1:8000/admin** untuk panel utama, atau **http://127.0.0.1:8000/superadmin** untuk panel superadmin.

> Setelah login, karena aplikasi memakai tenancy, URL beralih ke `admin/{id}/...` sesuai pasangan aktif.

### Akun bawaan (seeder)

| Email | Password | Role |
|-------|----------|------|
| admin@email.com | password | Superadmin |
| rehan@email.com | password | Suami |
| ayu@email.com | password | Istri |

## Menjalankan Transaksi Berulang Otomatis

```bash
php artisan schedule:work          # scheduler berjalan tiap hari 00:05
# atau manual kapan saja:
php artisan app:generate-recurring-transactions
```

## Testing

```bash
php artisan test    # atau: composer test
```

Test memakai SQLite in-memory (`refresh database` di setiap test).

## Panduan Penggunaan

Panduan lengkap penggunaan tiap menu tersedia di **[CARA_PAKAI.md](CARA_PAKAI.md)** — alur mencatat transaksi, anggaran, transaksi berulang, master data, hingga pengaturan superadmin.

## Struktur Proyek

```
app/
├── Exports/                 # Export Excel & PDF
├── Filament/
│   ├── Components/          # Komponen custom (mis. AvatarPicker)
│   ├── Pages/               # Dashboard, Profil, RegisterCouple
│   ├── Resources/           # Resource panel pasangan
│   ├── Superadmin/          # Resource & widget khusus panel superadmin
│   ├── Support/             # Helper (mis. PeruntukanOptions)
│   └── Widgets/             # Widget dashboard
├── Imports/                 # Import CSV transaksi
├── Models/                  # Eloquent models
└── Traits/LogsActivity.php  # Audit log otomatis
```

## Dokumentasi Terkait

- **[PRD.md](PRD.md)** — product requirements
- **[ISSUES.md](ISSUES.md)** — daftar isu, TODO, dan tech debt
- **[CARA_PAKAI.md](CARA_PAKAI.md)** — panduan pemakaian

## Lisensi

MIT — proyek ini berbasis [Laravel](https://laravel.com), yang dirilis di bawah [MIT license](https://opensource.org/licenses/MIT).