<?php

namespace App\Imports;

use App\Models\Budget;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TransactionsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected int $coupleId;

    public function __construct(?int $coupleId = null)
    {
        $this->coupleId = $coupleId ?? Filament::getTenant()?->getKey() ?? auth()->user()?->couple_id ?? 0;
    }

    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $tipe = strtolower(trim($row['tipe'] ?? ''));
        if (! in_array($tipe, ['pemasukan', 'pengeluaran'])) {
            return null;
        }

        $jumlah = (float) ($row['jumlah'] ?? 0);
        if ($jumlah <= 0) {
            return null;
        }

        $tanggal = $this->parseDate($row['tanggal'] ?? '');
        if (! $tanggal) {
            return null;
        }

        $peruntukanId = $this->resolveCategory($row, $tipe);
        if (! $peruntukanId) {
            return null;
        }

        $metodePembayaranId = $this->resolvePaymentMethod($row);
        if (! $metodePembayaranId) {
            return null;
        }

        $userId = $this->resolveUser($row);
        if (! $userId) {
            return null;
        }

        $budgetId = $this->resolveBudget($peruntukanId, $tanggal);

        return new Transaction([
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'tanggal' => $tanggal,
            'peruntukan_id' => $peruntukanId,
            'metode_pembayaran_id' => $metodePembayaranId,
            'user_id' => $userId,
            'couple_id' => $this->coupleId,
            'budget_id' => $budgetId,
            'deskripsi' => $row['deskripsi'] ?? null,
        ]);
    }

    protected function parseDate(string $value): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->format('Y-m-d');
            } catch (\Throwable) {
                // continue
            }
        }
        return null;
    }

    protected function resolveCategory(array $row, string $tipe): ?int
    {
        $nama = trim($row['kategori'] ?? $row['peruntukan'] ?? '');
        if (! $nama) {
            return null;
        }

        $category = Category::where(function ($q) {
            $q->where('couple_id', $this->coupleId)
                ->orWhereNull('couple_id');
        })
            ->where('is_active', true)
            ->where('tipe', $tipe)
            ->where('nama', $nama)
            ->first();

        return $category?->id;
    }

    protected function resolvePaymentMethod(array $row): ?int
    {
        $nama = trim($row['metode_pembayaran'] ?? $row['metode pembayaran'] ?? '');
        if (! $nama) {
            return null;
        }

        $method = PaymentMethod::where(function ($q) {
            $q->where('couple_id', $this->coupleId)
                ->orWhereNull('couple_id');
        })
            ->where('is_active', true)
            ->where('nama', $nama)
            ->first();

        return $method?->id;
    }

    protected function resolveUser(array $row): ?int
    {
        $email = trim($row['email'] ?? $row['user_email'] ?? '');
        if ($email) {
            $user = User::where('couple_id', $this->coupleId)->where('email', $email)->first();
            if ($user) {
                return $user->id;
            }
        }

        $name = trim($row['user'] ?? $row['dicatat_oleh'] ?? $row['dicatat oleh'] ?? '');
        if ($name) {
            $user = User::where('couple_id', $this->coupleId)->where('name', $name)->first();
            if ($user) {
                return $user->id;
            }
        }

        return auth()->id();
    }

    protected function resolveBudget(int $peruntukanId, string $tanggal): ?int
    {
        $bulan = Carbon::parse($tanggal)->month;
        $tahun = Carbon::parse($tanggal)->year;

        $budget = Budget::where('couple_id', $this->coupleId)
            ->where('peruntukan_id', $peruntukanId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        return $budget?->id;
    }

    public function rules(): array
    {
        return [
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required',
            'kategori' => 'required',
            'metode_pembayaran' => 'required',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'tipe.required' => 'Kolom tipe wajib diisi (pemasukan/pengeluaran).',
            'tipe.in' => 'Tipe harus pemasukan atau pengeluaran.',
            'jumlah.required' => 'Kolom jumlah wajib diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'tanggal.required' => 'Kolom tanggal wajib diisi.',
            'kategori.required' => 'Kolom kategori wajib diisi.',
            'metode_pembayaran.required' => 'Kolom metode pembayaran wajib diisi.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}