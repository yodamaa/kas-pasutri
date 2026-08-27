<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Budget;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $budgetId = $data['budget_id'] ?? null;

        if ($budgetId && ($data['tipe'] ?? '') === 'pengeluaran') {
            $budget = Budget::withCount([
                'transactions as terpakai' => function ($query) {
                    $now = now();
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                },
            ])->find($budgetId);

            if ($budget) {
                $sisa = $budget->jumlah - $budget->terpakai;
                if ($data['jumlah'] > $sisa) {
                    Notification::make()
                        ->title('Gagal!')
                        ->body('Jumlah melebihi sisa anggaran. Sisa: Rp ' . number_format($sisa, 0, ',', '.'))
                        ->danger()
                        ->send();

                    $this->halt();
                }
            }
        }

        return $data;
    }
}