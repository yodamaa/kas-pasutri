<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Budget;
use App\Models\Category;
use App\Models\PaymentMethod;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $coupleId = Filament::getTenant()?->getKey();

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
                    ->relationship('peruntukan', 'nama', fn ($query, $get) => $query->where('is_active', true)->where('tipe', $get('tipe')))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('budget_id', null)),
                Select::make('budget_id')
                    ->label('Pakai Anggaran?')
                    ->options(function ($get) use ($coupleId) {
                        $peruntukanId = $get('peruntukan_id');
                        if (!$peruntukanId) {
                            return [];
                        }

                        $tanggal = $get('tanggal');
                        $bulan = $tanggal ? Carbon::parse($tanggal)->month : now()->month;
                        $tahun = $tanggal ? Carbon::parse($tanggal)->year : now()->year;

                        $query = Budget::where('peruntukan_id', $peruntukanId)
                            ->where('bulan', $bulan)
                            ->where('tahun', $tahun);

                        if ($coupleId) {
                            $query->where('couple_id', $coupleId);
                        }

                        return $query->get()
                            ->mapWithKeys(function ($budget) {
                                $label = $budget->nama ? $budget->nama . ' — ' : '';
                                $label .= 'Rp ' . number_format($budget->sisa, 0, ',', '.') . ' tersisa dari Rp ' . number_format($budget->jumlah, 0, ',', '.');
                                if ($budget->persentase > 0) {
                                    $label .= ' (' . $budget->persentase . '%)';
                                }
                                return [
                                    $budget->id => $label,
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
                            $budget = Budget::find($state);
                            if ($budget) {
                                $set('jumlah', $budget->sisa);
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

                            $budget = Budget::find($budgetId);
                            if ($budget && $value > $budget->sisa) {
                                $fail('Jumlah melebihi sisa anggaran! Sisa tersisa: Rp ' . number_format($budget->sisa, 0, ',', '.'));
                            }
                        };
                    }),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('budget_id', null)),
                Select::make('metode_pembayaran_id')
                    ->label('Metode Pembayaran')
                    ->relationship('metodePembayaran', 'nama', fn ($query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Dicatat oleh')
                    ->relationship('user', 'name', fn ($query) => $coupleId ? $query->where('couple_id', $coupleId) : $query)
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
