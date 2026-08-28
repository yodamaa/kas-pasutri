<?php

namespace App\Filament\Resources\RecurringTransactions\Schemas;

use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RecurringTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $coupleId = Filament::getTenant()?->getKey();

        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('tipe')
                        ->label('Tipe Transaksi')
                        ->options([
                            'pemasukan' => 'Pemasukan',
                            'pengeluaran' => 'Pengeluaran',
                        ])
                        ->required()
                        ->live(),
                    TextInput::make('jumlah')
                        ->label('Jumlah (Rp)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->inputMode('numeric')
                        ->step(1000),
                ]),
                Grid::make(2)->schema([
                    Select::make('peruntukan_id')
                        ->label('Jenis Peruntukan')
                        ->options(function ($get) {
                            return Category::where('is_active', true)
                                ->when($get('tipe'), fn ($q, $tipe) => $q->where('tipe', $tipe))
                                ->pluck('nama', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),
                    Select::make('metode_pembayaran_id')
                        ->label('Metode Pembayaran')
                        ->relationship('metodePembayaran', 'nama', fn ($query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
                Select::make('user_id')
                    ->label('Dicatat oleh')
                    ->relationship('user', 'name', fn ($query) => $coupleId ? $query->where('couple_id', $coupleId) : $query)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->id()),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Contoh: Gaji bulanan, Cicilan rumah, dll')
                    ->rows(2),
                Grid::make(2)->schema([
                    Select::make('frequency')
                        ->label('Frekuensi')
                        ->options([
                            'daily' => 'Harian',
                            'weekly' => 'Mingguan',
                            'monthly' => 'Bulanan',
                            'yearly' => 'Tahunan',
                        ])
                        ->default('monthly')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('day_of_week', null)),
                    Select::make('day_of_week')
                        ->label('Hari')
                        ->visible(fn ($get) => $get('frequency') === 'weekly')
                        ->options([
                            0 => 'Minggu',
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                        ])
                        ->required()
                        ->live(),
                    Select::make('day_of_month')
                        ->label('Tanggal')
                        ->visible(fn ($get) => in_array($get('frequency'), ['monthly', 'yearly']))
                        ->options(array_combine(range(1, 31), range(1, 31)))
                        ->default(1)
                        ->required(),
                    Select::make('month')
                        ->label('Bulan')
                        ->visible(fn ($get) => $get('frequency') === 'yearly')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('starts_at')
                        ->label('Mulai Tanggal')
                        ->required()
                        ->default(now()),
                    DatePicker::make('ends_at')
                        ->label('Berakhir (opsional)')
                        ->default(null)
                        ->rules([
                            fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                                if ($value && $get('starts_at') && $value < $get('starts_at')) {
                                    $fail('Tanggal berakhir harus setelah tanggal mulai.');
                                }
                            },
                        ]),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
            ]);
    }
}
