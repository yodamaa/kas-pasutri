<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected ?string $tipe;
    protected ?int $bulan;
    protected ?int $tahun;

    public function __construct(?string $tipe = null, ?int $bulan = null, ?int $tahun = null)
    {
        $this->tipe = $tipe;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $query = Transaction::with(['peruntukan', 'metodePembayaran', 'user', 'budget'])
            ->orderBy('tanggal', 'desc');

        if ($this->tipe) {
            $query->where('tipe', $this->tipe);
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('tanggal', $this->tahun);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Kategori',
            'Jumlah (Rp)',
            'Metode Pembayaran',
            'Dicatat Oleh',
            'Anggaran',
            'Deskripsi',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->tanggal->format('d/m/Y'),
            $transaction->tipe === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran',
            $transaction->peruntukan->nama ?? '-',
            $transaction->jumlah,
            $transaction->metodePembayaran->nama ?? '-',
            $transaction->user->name ?? '-',
            $transaction->budget ? 'Rp ' . number_format($transaction->budget->jumlah, 0, ',', '.') : '-',
            $transaction->deskripsi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
