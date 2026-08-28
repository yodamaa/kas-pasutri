<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-recurring-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate transaksi berulang yang sudah jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $recurrings = RecurringTransaction::where('is_active', true)
            ->where('starts_at', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('last_generated_at')
                    ->orWhereDate('last_generated_at', '<', $today);
            })
            ->get();

        $total = 0;
        $detail = [];

        foreach ($recurrings as $recurring) {
            $created = $recurring->generateForMissingDates();
            if ($created > 0) {
                $total += $created;
                $detail[] = "  {$recurring->frequencyLabel()} - {$recurring->deskripsi}: {$created} transaksi";
            }
        }

        if ($total > 0) {
            $this->info("{$total} transaksi berulang berhasil dibuat:");
            foreach ($detail as $line) {
                $this->line($line);
            }
        } else {
            $this->info('Tidak ada transaksi berulang yang jatuh tempo.');
        }

        return self::SUCCESS;
    }
}
