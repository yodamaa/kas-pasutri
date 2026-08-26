# PRD: Aplikasi Keuangan Suami Istri (Uang Pasutri)

## 1. Overview

Aplikasi web manajemen keuangan keluarga dengan **Laravel 12 + Filament v5**. Untuk pasangan suami istri mencatat, mengelola, dan memantau keuangan bersama secara transparan.

---

## 2. Users

| Role | Description |
|------|-------------|
| **Suami** | Admin, bisa CRUD semua data |
| **Istri** | Admin, bisa CRUD semua data |

> Hanya 2 user. Tidak ada registrasi publik.

---

## 3. Fitur Utama

### 3.1 Dashboard
- **Stats Overview**: Total Pemasukan, Total Pengeluaran, Saldo Bersih, Jumlah Transaksi
- **Bar Chart**: Pemasukan vs Pengeluaran per Bulan (6 bulan terakhir)
- **Doughnut Chart**: Pengeluaran per Kategori Peruntukan
- **Budget Progress**: Progress bar anggaran per kategori
- **Tabel Transaksi Terakhir**: 5 transaksi terbaru

### 3.2 Jenis Pembayaran (Payment Methods)
- CRUD lengkap (nama, icon, warna, status aktif)

### 3.3 Jenis Peruntukan (Categories)
- CRUD dengan filter tipe (pemasukan/pengeluaran)

### 3.4 Transaksi (Transactions)
- CRUD dengan relasi ke kategori, metode pembayaran, user
- Upload lampiran (bukti/struk)
- Transaksi berulang (recurring)
- Filter: tipe, kategori, metode, user

### 3.5 Anggaran (Budgets)
- CRUD anggaran per kategori per bulan
- Progress bar di dashboard

---

## 4. Tech Stack

| Component | Tech |
|-----------|------|
| Backend | Laravel 12 |
| Admin Panel | Filament v5 |
| Database | MySQL 8.x (Laragon) |
| Charts | Filament built-in ChartWidget |
| Auth | Filament login |
| Currency | Rupiah (Rp), timezone Asia/Jakarta |

---

## 5. Login

- suami@email.com / password
- istri@email.com / password
