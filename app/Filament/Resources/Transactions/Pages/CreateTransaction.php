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
            $budget = Budget::find($budgetId);

            if ($budget && $data['jumlah'] > $budget->sisa) {
                Notification::make()
                    ->title('Gagal!')
                    ->body('Jumlah melebihi sisa anggaran. Sisa: Rp ' . number_format($budget->sisa, 0, ',', '.'))
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $budgetId = $this->data['budget_id'] ?? null;

        if ($budgetId && ($this->data['tipe'] ?? '') === 'pengeluaran') {
            $budget = Budget::find($budgetId);

            if ($budget) {
                if ($budget->isExceeded()) {
                    Notification::make()
                        ->title('Anggaran Habis!')
                        ->body("Anggaran \"{$budget->peruntukan->nama}\" telah melebihi batas ({$budget->persentase}%).")
                        ->danger()
                        ->send();
                } elseif ($budget->isThresholdExceeded()) {
                    Notification::make()
                        ->title('Peringatan Anggaran')
                        ->body("Anggaran \"{$budget->peruntukan->nama}\" telah mencapai {$budget->persentase}% (ambang: {$budget->alert_threshold}%).")
                        ->warning()
                        ->send();
                }
            }
        }
    }
}