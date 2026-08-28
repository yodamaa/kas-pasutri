<?php

namespace App\Filament\Resources\RecurringTransactions\Pages;

use App\Filament\Resources\RecurringTransactions\RecurringTransactionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRecurringTransaction extends EditRecord
{
    protected static string $resource = RecurringTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Sekarang')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Transaksi Berulang')
                ->modalDescription('Buat transaksi untuk tanggal yang sudah jatuh tempo namun belum dibuat.')
                ->action(function () {
                    $count = $this->record->generateForMissingDates();

                    Notification::make()
                        ->title('Selesai')
                        ->body($count > 0
                            ? "{$count} transaksi berhasil dibuat."
                            : 'Tidak ada transaksi baru yang perlu dibuat.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $count = $this->record->generateForMissingDates();

        if ($count > 0) {
            Notification::make()
                ->title("{$count} transaksi berulang baru dibuat otomatis.")
                ->info()
                ->send();
        }
    }
}
