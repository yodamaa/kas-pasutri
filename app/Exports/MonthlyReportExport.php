<?php

namespace App\Exports;

use App\Models\Budget;
use App\Models\Couple;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyReportExport
{
    protected array $bulanLabel = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function download(int $coupleId, int $bulan, int $tahun)
    {
        $couple = Couple::findOrFail($coupleId);
        $bulanLabel = $this->bulanLabel[$bulan] ?? (string) $bulan;

        $transactions = Transaction::where('couple_id', $coupleId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $pemasukan = $transactions->where('tipe', 'pemasukan');
        $pengeluaran = $transactions->where('tipe', 'pengeluaran');

        $totalPemasukan = (float) $pemasukan->sum('jumlah');
        $totalPengeluaran = (float) $pengeluaran->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;
        $jumlahPemasukan = $pemasukan->count();
        $jumlahPengeluaran = $pengeluaran->count();

        $pemasukanPerKategori = $pemasukan
            ->groupBy('peruntukan_id')
            ->map(fn ($items) => (object) [
                'nama' => $items->first()->peruntukan?->nama ?? '-',
                'jumlah_transaksi' => $items->count(),
                'total' => (float) $items->sum('jumlah'),
            ])
            ->values();

        $budgets = Budget::with('peruntukan')
            ->where('couple_id', $coupleId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $pengeluaranPerKategori = $pengeluaran
            ->groupBy('peruntukan_id')
            ->map(function ($items) use ($budgets) {
                $peruntukanId = $items->first()->peruntukan_id;
                $budget = $budgets->where('peruntukan_id', $peruntukanId)->first();
                $total = (float) $items->sum('jumlah');
                $anggaran = $budget ? (float) $budget->jumlah : 0;

                return (object) [
                    'nama' => $items->first()->peruntukan?->nama ?? '-',
                    'jumlah_transaksi' => $items->count(),
                    'total' => $total,
                    'anggaran' => $anggaran,
                    'persentase' => $anggaran > 0 ? round(($total / $anggaran) * 100, 1) : 0,
                ];
            })
            ->values();

        $totalTransaksi = $transactions->count();
        $totalAnggaran = (float) $budgets->sum('jumlah');
        $totalRealisasi = (float) $transactions->where('tipe', 'pengeluaran')->sum('jumlah');
        $persentaseAnggaran = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0;

        $pdf = Pdf::loadView('filament.exports.monthly-report', compact(
            'couple', 'bulan', 'bulanLabel', 'tahun',
            'totalPemasukan', 'totalPengeluaran', 'saldo',
            'jumlahPemasukan', 'jumlahPengeluaran', 'totalTransaksi',
            'totalAnggaran', 'totalRealisasi', 'persentaseAnggaran',
            'pemasukanPerKategori', 'pengeluaranPerKategori'
        ));

        $filename = "laporan-{$bulan}-{$tahun}.pdf";

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename
        );
    }
}
