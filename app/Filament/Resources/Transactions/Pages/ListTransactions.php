<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
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
                    if ($tipe) $filename .= '_' . $tipe;
                    if ($bulan) $filename .= '_bulan' . $bulan;
                    if ($tahun) $filename .= '_' . $tahun;
                    $filename .= '.xlsx';

                    return Excel::download(new TransactionExport($tipe, $bulan, $tahun), $filename);
                }),
        ];
    }
}
