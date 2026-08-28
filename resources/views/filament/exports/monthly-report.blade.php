<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        h2 { font-size: 13px; margin: 20px 0 6px; border-bottom: 2px solid #d1d5db; padding-bottom: 4px; }
        .muted { color: #6b7280; font-size: 10px; }
        .header { border-bottom: 3px solid #f59e0b; padding-bottom: 8px; margin-bottom: 12px; }
        .meta { display: block; margin-top: 4px; }
        .summary td { padding: 6px 8px; }
        .summary-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; margin-bottom: 18px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #f3f4f6; text-align: left; padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 10px; text-transform: uppercase; }
        table.data td { padding: 6px 8px; border: 1px solid #e5e7eb; }
        .num { text-align: right; }
        .green { color: #059669; font-weight: bold; }
        .red { color: #dc2626; font-weight: bold; }
        .total-row td { font-weight: bold; background: #f9fafb; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan Bulanan</h1>
        <span class="muted">{{ $couple->nama }} &mdash; {{ $bulanLabel }} {{ $tahun }}</span>
        <span class="meta muted">Dibuat: {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    <div class="summary-box">
        <table class="summary">
            <tr>
                <td><strong>Total Pemasukan</strong></td>
                <td class="num green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                <td style="width:30px"></td>
                <td><strong>Total Pengeluaran</strong></td>
                <td class="num red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Saldo Bersih</strong></td>
                <td class="num {{ $saldo < 0 ? 'red' : 'green' }}">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                <td></td>
                <td><strong>Total Transaksi</strong></td>
                <td class="num">{{ $totalTransaksi }} ({{ $jumlahPemasukan }} masuk / {{ $jumlahPengeluaran }} keluar)</td>
            </tr>
            <tr>
                <td><strong>Total Anggaran</strong></td>
                <td class="num">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                <td></td>
                <td><strong>Realisasi Anggaran</strong></td>
                <td class="num {{ $persentaseAnggaran >= 100 ? 'red' : '' }}">Rp {{ number_format($totalRealisasi, 0, ',', '.') }} ({{ number_format($persentaseAnggaran, 1) }}%)</td>
            </tr>
        </table>
    </div>

    <h2>Pemasukan per Kategori</h2>
    @if ($pemasukanPerKategori->isEmpty())
        <p class="muted">Tidak ada pemasukan pada periode ini.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="num">Jumlah Transaksi</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pemasukanPerKategori as $row)
                    <tr>
                        <td>{{ $row->nama }}</td>
                        <td class="num">{{ $row->jumlah_transaksi }}</td>
                        <td class="num green">+Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td class="num">{{ $jumlahPemasukan }}</td>
                    <td class="num green">+Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <h2>Pengeluaran per Kategori vs Anggaran</h2>
    @if ($pengeluaranPerKategori->isEmpty())
        <p class="muted">Tidak ada pengeluaran pada periode ini.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="num">Jumlah Transaksi</th>
                    <th class="num">Total</th>
                    <th class="num">Anggaran</th>
                    <th class="num">Realisasi</th>
                    <th class="num">Sisa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengeluaranPerKategori as $row)
                    <tr>
                        <td>{{ $row->nama }}</td>
                        <td class="num">{{ $row->jumlah_transaksi }}</td>
                        <td class="num red">-Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($row->anggaran, 0, ',', '.') }}</td>
                        <td class="num {{ $row->persentase >= 100 ? 'red' : '' }}">{{ number_format($row->persentase, 1) }}%</td>
                        <td class="num">Rp {{ number_format(max($row->anggaran - $row->total, 0), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td class="num">{{ $jumlahPengeluaran }}</td>
                    <td class="num red">-Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($persentaseAnggaran, 1) }}%</td>
                    <td class="num">Rp {{ number_format(max($totalAnggaran - $totalRealisasi, 0), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">Dicetak dari {{ config('app.name') }}</div>
</body>
</html>