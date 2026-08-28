<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Exports\MonthlyReportExport;
use App\Exports\TransactionExport;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Imports\TransactionsImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Select::make('export_tipe')
                        ->label('Tipe')
                        ->options([
                            '' => 'Semua',
                            'pemasukan' => 'Pemasukan',
                            'pengeluaran' => 'Pengeluaran',
                        ])
                        ->default(''),
                    Select::make('export_bulan')
                        ->label('Bulan')
                        ->options([
                            '' => 'Semua',
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month),
                    Select::make('export_tahun')
                        ->label('Tahun')
                        ->options([
                            '' => 'Semua',
                            now()->year => (string) now()->year,
                            now()->year - 1 => (string) (now()->year - 1),
                        ])
                        ->default(now()->year),
                ])
                ->action(function (array $data) {
                    $tipe = $data['export_tipe'] ?: null;
                    $bulan = $data['export_bulan'] ?: null;
                    $tahun = $data['export_tahun'] ?: null;

                    $filename = 'transaksi';
                    if ($tipe) {
                        $filename .= '_'.$tipe;
                    }
                    if ($bulan) {
                        $filename .= '_bulan'.$bulan;
                    }
                    if ($tahun) {
                        $filename .= '_'.$tahun;
                    }
                    $filename .= '.xlsx';

                    return Excel::download(new TransactionExport($tipe, $bulan, $tahun), $filename);
                }),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->form([
                    Select::make('pdf_bulan')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),
                    Select::make('pdf_tahun')
                        ->label('Tahun')
                        ->options([
                            now()->year => (string) now()->year,
                            now()->year - 1 => (string) (now()->year - 1),
                            now()->year - 2 => (string) (now()->year - 2),
                        ])
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    return app(MonthlyReportExport::class)->download(
                        Filament::getTenant()?->getKey() ?? 0,
                        (int) $data['pdf_bulan'],
                        (int) $data['pdf_tahun'],
                    );
                }),
            Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    FileUpload::make('file')
                        ->label('File CSV')
                        ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                        ->required()
                        ->helperText('Format kolom: tipe, jumlah, tanggal (d/m/Y), kategori, metode_pembayaran, email (opsional), deskripsi (opsional). Kategori & metode pembayaran harus sudah ada di sistem.'),
                ])
                ->action(function (array $data) {
                    $import = new TransactionsImport;
                    $import->import($data['file']);

                    Notification::make()
                        ->title('Import Selesai')
                        ->body('Data transaksi berhasil diimpor.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
