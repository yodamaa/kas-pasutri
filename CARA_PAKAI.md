# Panduan Penggunaan - Uang Pasutri

## Memulai Aplikasi

### 1. Jalankan Server

```bash
cd D:\REHAN\laragon\www\project-uang-pasutri
php artisan serve
```

Buka browser: **http://127.0.0.1:8000/admin**

> **Alternatif (jika domain sudah terdaftar di hosts):** `http://project-uang-pasutri.test/admin`
> (Apache Laragon, port 80). Catatan: aplikasi memakai multi-pasangan (tenancy), jadi
> setelah login halaman akan beralih ke `admin/{id}/...` sesuai pasangan yang dipilih.

### 2. Login

| Email | Password | Role |
|-------|----------|------|
| admin@email.com | password | Superadmin |
| rehan@email.com | password | Suami |
| ayu@email.com | password | Istri |

> `admin@email.com` bisa mengelola pengaturan & master data global. Suami/Istri
> fokus ke pencatatan transaksi pasangannya masing-masing.

---

## Menu Navigasi

- **Dashboard** - ringkasan keuangan pasangan aktif
- **Transaksi**
  - Transaksi - pencatatan pemasukan/pengeluaran
  - Anggaran - batas pengeluaran per kategori
  - Transaksi Berulang - transaksi rutin otomatis
- **Master Data** (global)
  - Jenis Pembayaran
  - Jenis Peruntukan
- **Pengaturan** (khusus Superadmin)
  - Pasangan - kelola pasangan & status aktif
  - User - kelola akun suami/istri (termasuk foto profil)
  - Log Aktivitas - riwayat perubahan data
- **Profil** (ikon user di pojok kanan atas) - ubah foto profil & data akun

---

## Dashboard

Halaman utama yang menampilkan:
- **Total Pemasukan** - jumlah uang masuk bulan ini
- **Total Pengeluaran** - jumlah uang keluar bulan ini
- **Saldo Bersih** - selisih pemasukan dan pengeluaran
- **Jumlah Transaksi** - total transaksi bulan ini
- **Grafik Bar** - perbandingan pemasukan vs pengeluaran 6 bulan terakhir
- **Grafik Doughnut** - distribusi pengeluaran per kategori
- **Anggaran** - progress bar anggaran per kategori
- **Transaksi Terakhir** - 5 transaksi paling baru

---

## Transaksi

#### Transaksi (`/admin/{id}/transactions`)

**Cara tambah transaksi:**
1. Klik tombol **+ New**
2. Pilih **Tipe Transaksi**: Pemasukan atau Pengeluaran
3. Pilih **Jenis Peruntukan** dari dropdown
4. **Pakai Anggaran?** (opsional, hanya untuk pengeluaran) - pilih anggaran bulan ini;
   jumlah otomatis terisi sisa anggaran dan tidak boleh melebihi sisanya
5. Isi **Jumlah (Rp)** (contoh: 500000)
6. Pilih **Tanggal**
7. Pilih **Metode Pembayaran** dari dropdown
8. Pilih **Dicatat oleh** (Suami/Istri)
9. Isi **Deskripsi** (opsional, catatan tambahan)
10. Upload **Lampiran** (opsional, foto bukti/struk)
11. **Transaksi Berulang** (opsional) - aktifkan untuk transaksi rutin, lalu pilih
    **Interval Berulang** (Harian/Mingguan/Bulanan/Tahunan)
12. Klik **Create**

**Fitur tambahan:**
- **Export PDF** - tombol di toolbar untuk mengunduh laporan bulanan; pilih bulan
  dan tahun lalu unduh
- **Recycle bin** - transaksi yang dihapus masuk ke *trashed*; pakai filter
  **Sampah** untuk melihat, bisa **pulihkan** atau **hapus permanen**

**Fitur filter di tabel:**
- Filter berdasarkan **Tipe** (Pemasukan/Pengeluaran)
- Filter berdasarkan **Peruntukan** (kategori)
- Filter berdasarkan **Metode Pembayaran**
- Filter berdasarkan **Dicatat oleh** (user)

**Tip:** Kolom jumlah berwarna hijau untuk pemasukan (+) dan merah untuk pengeluaran (-)

---

## Transaksi Berulang

#### Transaksi Berulang (`/admin/{id}/recurring-transactions`)

Untuk transaksi rutin (gaji, cicilan, tagihan).

**Cara tambah:**
1. Klik tombol **+ New**
2. Isi **Tipe Transaksi**, **Jumlah (Rp)**, **Jenis Peruntukan**, dan **Metode Pembayaran**
3. Pilih **Dicatat oleh**
4. Isi **Deskripsi** (opsional)
5. Pilih **Frekuensi**:
   - **Harian** - dijalankan setiap hari
   - **Mingguan** - pilih **Hari** (Minggu–Sabtu)
   - **Bulanan** - pilih **Tanggal** (1–31)
   - **Tahunan** - pilih **Tanggal** dan **Bulan**
6. Atur **Mulai Tanggal** dan **Berakhir** (opsional)
7. Aktifkan toggle **Aktif** bila transaksi ini berjalan
8. Klik **Create**

> **Catatan:** Pembuatan transaksi otomatis dijalankan scheduler Laravel
> (setiap hari 00:05). Pastikan `php artisan schedule:work` berjalan, atau
> jalankan manual kapan saja lewat `php artisan app:generate-recurring-transactions`.

---

## Anggaran

#### Anggaran (`/admin/{id}/budgets`)

Menetapkan batas pengeluaran per kategori per bulan.

**Cara tambah anggaran:**
1. Klik tombol **+ New**
2. Pilih **Jenis Peruntukan** dari dropdown
3. Isi **Anggaran (Rp)** (contoh: 2000000)
4. Pilih **Bulan** (Januari - Desember)
5. Isi **Tahun** (contoh: 2026)
6. Klik **Create**

**Di Dashboard:**
- Progress bar akan menampilkan persentase anggaran yang sudah terpakai
- Warna hijau: masih aman (< 80%)
- Warna kuning: mendekati batas (80-99%)
- Warna merah: sudah terlampaui (100%)

---

## Master Data

#### Jenis Pembayaran (`/admin/{id}/payment-methods`)
Mengelola metode pembayaran yang digunakan (global untuk semua pasangan).

**Cara tambah:**
1. Klik tombol **+ New**
2. Isi **Nama Metode** (contoh: Transfer BCA)
3. Isi **Icon** dengan emoji (contoh: 🏦)
4. Pilih **Warna**
5. Aktifkan **Toggle Aktif**
6. Klik **Create**

**Contoh data:**
- Tunai 💵
- Transfer BCA 🏦
- GoPay 💚
- OVO 💜
- Kartu Kredit 💳

---

#### Jenis Peruntukan (`/admin/{id}/categories`)
Mengelola kategori tujuan pemasukan/pengeluaran (global).

**Cara tambah:**
1. Klik tombol **+ New**
2. Isi **Nama Kategori** (contoh: Makanan & Minuman)
3. Pilih **Tipe**: Pemasukan atau Pengeluaran
4. Isi **Icon** dengan emoji (contoh: 🍔)
5. Pilih **Warna**
6. Aktifkan **Toggle Aktif**
7. Klik **Create**

**Filter:** Gunakan dropdown di tabel untuk filter berdasarkan tipe (Pemasukan/Pengeluaran)

**Contoh Kategori Pengeluaran:**
- Makanan & Minuman 🍔
- Transportasi 🚗
- Tagihan Listrik 💡
- Kesehatan 🏥

**Contoh Kategori Pemasukan:**
- Gaji 💰
- Bonus 🎁
- Hasil Usaha 💼

---

## Pengaturan (Superadmin)

#### Pasangan (`/admin/{id}/couples`)
Kelola pasangan yang terdaftar.

**Cara tambah pasangan baru:**
1. Klik tombol **+ New** (atau buka **Buat Pasangan** di halaman pemilih pasangan)
2. Isi **Nama Pasangan** (contoh: Ahmad & Fatimah)
3. Isi **Kode** unik (contoh: PAS001)
4. Aktifkan **Toggle Aktif**
5. Klik **Create**

Tabel menampilkan kode, nama, jumlah member, dan status aktif.

#### User (`/admin/{id}/users`)
Kelola akun suami/istri dan superadmin.

**Cara tambah user:**
1. Klik tombol **+ New**
2. Upload/ambil **Foto Profil** (opsional)
3. Isi **Nama**, **Email**, dan **Password**
4. Pilih **Role**: Superadmin / Suami / Istri
5. Saat role Suami/Istri, pilih **Pasangan**-nya di dropdown
6. Aktifkan **Toggle Aktif**
7. Klik **Create**

#### Log Aktivitas (`/admin/{id}/activity-logs`)
Riwayat otomatis setiap pembuatan/perubahan/penghapusan data (Waktu, User, Event,
Model, Data Lama & Baru).

---

## Profil & Foto Avatar

Menu **Profil** (klik ikon user di pojok kanan atas):

1. Klik **Profil**
2. Pada **Foto Profil**, pilih **Unggah** atau pilih dari **galeri** yang tersedia
3. Klik terapkan/unggah foto, lalu **Simpan**
4. Nama, email, dan password juga bisa diubah di halaman ini

Foto profil langsung tampil di tabel User (Superadmin) dan di menu akun Anda.

---

## Contoh Skenario Penggunaan

### Skenario 1: Mencatat Gaji
1. Buka menu **Transaksi** → klik **+ New**
2. Tipe: **Pemasukan**
3. Peruntukan: **Gaji**
4. Jumlah: **8000000**
5. Tanggal: pilih tanggal gajian
6. Metode Pembayaran: **Transfer BCA**
7. Dicatat oleh: pilih nama
8. Klik **Create**

### Skenario 2: Mencatat Belanja Bulanan
1. Buka menu **Transaksi** → klik **+ New**
2. Tipe: **Pengeluaran**
3. Peruntukan: **Makanan & Minuman**
4. Jumlah: **500000**
5. Tanggal: hari ini
6. Metode Pembayaran: **GoPay**
7. Dicatat oleh: pilih nama
8. Deskripsi: "Belanja mingguan di pasar"
9. Klik **Create**

### Skenario 3: Membuat Anggaran
1. Buka menu **Anggaran** → klik **+ New**
2. Peruntukan: **Makanan & Minuman**
3. Anggaran: **2000000**
4. Bulan: **Agustus**
5. Tahun: **2026**
6. Klik **Create**
7. Cek dashboard untuk melihat progress bar anggaran

### Skenario 4: Tagihan Rutin Bulanan
1. Buka menu **Transaksi Berulang** → klik **+ New**
2. Tipe: **Pengeluaran**
3. Peruntukan: **Tagihan Listrik**
4. Jumlah: **350000**
5. Frekuensi: **Bulanan**, Tanggal: **5**
6. Mulai Tanggal: bulan ini, toggle **Aktif** on
7. Klik **Create**
8. Transaksi baru akan dibuat otomatis setiap tanggal jatuh tempo (jalankan
   `php artisan schedule:work` agar berjalan sendiri; atau `php artisan app:generate-recurring-transactions` untuk menjalankan manual)

---

## Tips

- **Gunakan emoji** pada icon untuk mempermudah identifikasi visual
- **Isi deskripsi** pada transaksi untuk catatan detail
- **Upload lampiran** (foto struk/bukti) untuk pencatatan lebih lengkap
- **Buat anggaran** untuk setiap kategori pengeluaran utama
- **Pakai transaksi berulang** untuk tagihan rutin agar pencatatan ringkas
- **Cek dashboard secara berkala** untuk memantau kondisi keuangan
- **Gunakan filter** di tabel transaksi untuk pencarian data
- **Pasang foto profil** agar Dicatat oleh lebih mudah dikenali

---

## Format Data

| Item | Format |
|------|--------|
| Mata Uang | Rupiah (Rp) |
| Format Angka | 1.000.000 (titik sebagai pemisah ribuan) |
| Tanggal | dd/mm/yyyy |
| Timezone | Asia/Jakarta (WIB) |
| Bahasa | Indonesia |