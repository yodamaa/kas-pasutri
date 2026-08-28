<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Budget;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $budgetId = $data['budget_id'] ?? null;

        if ($budgetId && ($data['tipe'] ?? '') === 'pengeluaran') {
            $budget = Budget::find($budgetId);

            if ($budget) {
                $sisa = $budget->sisa;

                if ($this->record) {
                    $oldBudgetId = $this->record->budget_id;
                    if ($oldBudgetId == $budgetId) {
                        $sisa += $this->record->jumlah;
                    }
                }

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

    protected function afterSave(): void
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
