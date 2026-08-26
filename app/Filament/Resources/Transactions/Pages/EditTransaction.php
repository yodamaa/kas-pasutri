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
            $budget = Budget::withCount([
                'transactions as terpakai' => function ($query) {
                    $now = now();
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                },
            ])->find($budgetId);

            if ($budget) {
                $sisa = $budget->jumlah - $budget->terpakai;

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
}
