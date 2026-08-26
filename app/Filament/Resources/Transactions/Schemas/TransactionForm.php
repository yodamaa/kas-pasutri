<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Budget;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipe')
                    ->label('Tipe Transaksi')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('budget_id', null)),
                Select::make('peruntukan_id')
                    ->label('Jenis Peruntukan')
                    ->relationship('peruntukan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('budget_id', null))
                    ->options(fn ($get) => Category::where('tipe', $get('tipe') ?? 'pengeluaran')->pluck('nama', 'id')),
                Select::make('budget_id')
                    ->label('Pakai Anggaran?')
                    ->options(function ($get) {
                        $peruntukanId = $get('peruntukan_id');
                        if (!$peruntukanId) {
                            return [];
                        }

                        $now = now();

                        return Budget::where('peruntukan_id', $peruntukanId)
                            ->where('bulan', $now->month)
                            ->where('tahun', $now->year)
                            ->withCount([
                                'transactions as terpakai' => function ($query) use ($now) {
                                    $query->whereMonth('created_at', $now->month)
                                        ->whereYear('created_at', $now->year);
                                },
                            ])
                            ->get()
                            ->mapWithKeys(function ($budget) {
                                $sisa = $budget->jumlah - $budget->terpakai;
                                return [
                                    $budget->id => 'Rp ' . number_format($sisa, 0, ',', '.') . ' tersisa dari Rp ' . number_format($budget->jumlah, 0, ',', '.'),
                                ];
                            });
                    })
                    ->visible(fn ($get) => $get('tipe') === 'pengeluaran' && $get('peruntukan_id'))
                    ->live()
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(null)
                    ->afterStateUpdated(function ($get, $set, $state) {
                        if ($state) {
                            $budget = Budget::withCount([
                                'transactions as terpakai' => function ($query) {
                                    $now = now();
                                    $query->whereMonth('created_at', $now->month)
                                        ->whereYear('created_at', $now->year);
                                },
                            ])->find($state);

                            if ($budget) {
                                $sisa = $budget->jumlah - $budget->terpakai;
                                $set('jumlah', $sisa);
                            }
                        }
                    }),
                TextInput::make('jumlah')
                    ->label('Jumlah (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->inputMode('numeric')
                    ->step(1000)
                    ->validationMessages(['required' => 'Jumlah wajib diisi.'])
                    ->rules(function ($get) {
                        return function ($attribute, $value, $fail) use ($get) {
                            $budgetId = $get('budget_id');
                            if (!$budgetId || !$value) {
                                return;
                            }

                            $budget = Budget::withCount([
                                'transactions as terpakai' => function ($query) {
                                    $now = now();
                                    $query->whereMonth('created_at', $now->month)
                                        ->whereYear('created_at', $now->year);
                                },
                            ])->find($budgetId);

                            if ($budget) {
                                $sisa = $budget->jumlah - $budget->terpakai;
                                if ($value > $sisa) {
                                    $fail('Jumlah melebihi sisa anggaran! Sisa tersisa: Rp ' . number_format($sisa, 0, ',', '.'));
                                }
                            }
                        };
                    }),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),
                Select::make('metode_pembayaran_id')
                    ->label('Metode Pembayaran')
                    ->relationship('metodePembayaran', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Dicatat oleh')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Catatan tambahan...')
                    ->rows(3),
                FileUpload::make('lampiran')
                    ->label('Lampiran (Bukti/Struk)')
                    ->image()
                    ->directory('lampiran-transaksi')
                    ->nullable(),
                Toggle::make('is_recurring')
                    ->label('Transaksi Berulang')
                    ->default(false),
                Select::make('recurring_interval')
                    ->label('Interval Berulang')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'yearly' => 'Tahunan',
                    ])
                    ->visible(fn ($get) => $get('is_recurring')),
            ]);
    }
}
