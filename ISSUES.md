# Issues & TODO - Uang Pasutri

## Known Issues

### 1. BudgetTransactions Relationship Cache
- **Status**: Fixed
- **Deskripsi**: `Budget::transactions()` tidak ditemukan saat pertama kali dijalankan
- **Solusi**: Relasi `transactions()` sudah ditambahkan ke `App\Models\Budget`
- **Note**: Jika masih error, coba jalankan `php artisan view:clear && php artisan config:clear && php artisan cache:clear`

### 2. Export Excel - RedirectAfterDownload
- **Status**: Potential Issue
- **Deskripsi**: Setelah download Excel, Filament bisa redirect ke halaman yang salah
- **Fix**: Pastikan action `->action()` return `Excel::download()` bukan void

### 3. TransactionForm - Kategori Tipe Filter
- **Status**: Minor
- **Deskripsi**: Dropdown "Jenis Peruntukan" difilter berdasarkan tipe transaksi, tapi filter `->options()` memanggil query setiap kali state berubah
- **Optimasi**: Pertimbangkan cache atau lazy loading

---

## TODO / Fitur yang Belum Dibuat

### High Priority
- [ ] **Validation budget_id** - Saat edit transaksi, pastikan budget_id yang dipilih masih valid untuk bulan/tahun transaksi
- [ ] **Soft delete transaksi** - Agar data tidak hilang total saat dihapus
- [ ] **Laporan bulanan PDF** - Export laporan ke format PDF
- [ ] **Recurring transaction automation** - Otomatis buat transaksi berulang tiap periode

### Medium Priority
- [ ] **Dashboard filter bulan/tahun** - Saat ini dashboard hanya menampilkan data bulan ini
- [ ] **Budget alert notifikasi** - Notifikasi saat anggaran mendekati batas
- [ ] **Multiple budget per kategori** - Saat ini 1 kategori = 1 budget per bulan
- [ ] **Import transaksi dari CSV** - Fitur upload bulk transaksi

### Low Priority
- [x] **Dark mode** - Toggle mode gelap ✅ Done
- [x] **Mobile responsive** - Optimasi tampilan untuk HP ✅ Done
- [x] **Role management** - Selain suami/istri, tambah role lain (opsional) ✅ Done
- [x] **Audit log** - Catatan siapa yang mengubah data kapan ✅ Done

---

## Tech Debt
- Public assets (CSS/JS/Fonks) di-commit ke git. Pertimbangkan build dengan Vite hanya file yang diperlukan
- `TransactionsTable` uses `recordActions` dan `toolbarActions` - pastikan API ini masih valid di Filament v5
- Widget `BudgetOverviewWidget` menggunakan raw Blade view (`budget-overview.blade.php`) - pertimbangkan refactor ke component
