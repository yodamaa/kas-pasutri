# Panduan Penggunaan - Uang Pasutri

## Memulai Aplikasi

### 1. Jalankan Server

```bash
cd D:\REHAN\laragon\www\project-uang-pasutri
php artisan serve
```

Buka browser: **http://localhost:8000/admin**

### 2. Login

| Email | Password | Role |
|-------|----------|------|
| suami@email.com | password | Suami |
| istri@email.com | password | Istri |

---

## Menu Navigasi

### Dashboard (`/admin`)
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

### Master Data

#### Jenis Pembayaran (`/admin/payment-methods`)
Mengelola metode pembayaran yang digunakan.

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

#### Jenis Peruntukan (`/admin/categories`)
Mengelola kategori tujuan pemasukan/pengeluaran.

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

### Transaksi

#### Transaksi (`/admin/transactions`)
Halaman utama pencatatan keuangan.

**Cara tambah transaksi:**
1. Klik tombol **+ New**
2. Pilih **Tipe Transaksi**: Pemasukan atau Pengeluaran
3. Isi **Jumlah (Rp)** (contoh: 500000)
4. Pilih **Tanggal**
5. Pilih **Jenis Peruntukan** dari dropdown
6. Pilih **Metode Pembayaran** dari dropdown
7. Pilih **Dicatat oleh** (Suami/Istri)
8. Isi **Deskripsi** (opsional, catatan tambahan)
9. Upload **Lampiran** (opsional, foto bukti/struk)
10. Aktifkan **Transaksi Berulang** jika transaksi rutin (opsional)
11. Klik **Create**

**Fitur filter di tabel:**
- Filter berdasarkan **Tipe** (Pemasukan/Pengeluaran)
- Filter berdasarkan **Peruntukan** (kategori)
- Filter berdasarkan **Metode Pembayaran**
- Filter berdasarkan **Dicatat oleh** (user)

**Tip:** Kolom jumlah berwarna hijau untuk pemasukan (+) dan merah untuk pengeluaran (-)

---

#### Anggaran (`/admin/budgets`)
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

## Contoh Skenario Penggunaan

### Skenario 1: Mencatat Gaji
1. Buka menu **Transaksi** → klik **+ New**
2. Tipe: **Pemasukan**
3. Jumlah: **8000000**
4. Tanggal: pilih tanggal gajian
5. Peruntukan: **Gaji**
6. Metode Pembayaran: **Transfer BCA**
7. Dicatat oleh: pilih nama
8. Klik **Create**

### Skenario 2: Mencatat Belanja Bulanan
1. Buka menu **Transaksi** → klik **+ New**
2. Tipe: **Pengeluaran**
3. Jumlah: **500000**
4. Tanggal: hari ini
5. Peruntukan: **Makanan & Minuman**
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

---

## Tips

- **Gunakan emoji** pada icon untuk mempermudah identifikasi visual
- **Isi deskripsi** pada transaksi untuk catatan detail
- **Upload lampiran** (foto struk/bukti) untuk pencatatan lebih lengkap
- **Buat anggaran** untuk setiap kategori pengeluaran utama
- **Cek dashboard secara berkala** untuk memantau kondisi keuangan
- **Gunakan filter** di tabel transaksi untuk pencarian data

---

## Format Data

| Item | Format |
|------|--------|
| Mata Uang | Rupiah (Rp) |
| Format Angka | 1.000.000 (titik sebagai pemisah ribuan) |
| Tanggal | dd/mm/yyyy |
| Timezone | Asia/Jakarta (WIB) |
| Bahasa | Indonesia |
