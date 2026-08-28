<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'couple_id',
        'user_id',
        'peruntukan_id',
        'metode_pembayaran_id',
        'tipe',
        'jumlah',
        'deskripsi',
        'frequency',
        'day_of_week',
        'day_of_month',
        'month',
        'starts_at',
        'ends_at',
        'last_generated_at',
        'is_active',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'month' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'last_generated_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peruntukan()
    {
        return $this->belongsTo(Category::class, 'peruntukan_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(PaymentMethod::class, 'metode_pembayaran_id');
    }

    public function frequencyLabel(): string
    {
        return match ($this->frequency) {
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
            default => ucfirst($this->frequency),
        };
    }

    public function dayLabel(): string
    {
        return match ($this->frequency) {
            'weekly' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$this->day_of_week] ?? '-',
            'monthly' => 'Tanggal '.$this->day_of_month,
            'yearly' => $this->month.'/'.$this->day_of_month,
            default => '-',
        };
    }

    public function isOccurrenceDate(Carbon $date): bool
    {
        return match ($this->frequency) {
            'daily' => true,
            'weekly' => $this->day_of_week !== null && $date->dayOfWeek === (int) $this->day_of_week,
            'monthly' => $this->day_of_month !== null
                && $date->day === min((int) $this->day_of_month, $date->daysInMonth),
            'yearly' => $this->month !== null && $this->day_of_month !== null
                && $date->month === (int) $this->month
                && $date->day === min((int) $this->day_of_month, $date->daysInMonth),
            default => false,
        };
    }

    public function generateForMissingDates(): int
    {
        $last = $this->last_generated_at
            ? Carbon::parse($this->last_generated_at)->addDay()
            : Carbon::parse($this->starts_at);

        $end = $this->ends_at
            ? Carbon::parse($this->ends_at)->min(Carbon::today())
            : Carbon::today();

        if ($last->gt($end)) {
            return 0;
        }

        $cursor = $last->copy();
        $count = 0;
        $guard = 0;

        while ($cursor->lte($end) && $count < 500 && $guard < 2000) {
            $guard++;

            if ($this->isOccurrenceDate($cursor)) {
                Transaction::create([
                    'tipe' => $this->tipe,
                    'jumlah' => $this->jumlah,
                    'tanggal' => $cursor->toDateString(),
                    'deskripsi' => $this->deskripsi,
                    'peruntukan_id' => $this->peruntukan_id,
                    'metode_pembayaran_id' => $this->metode_pembayaran_id,
                    'user_id' => $this->user_id,
                    'couple_id' => $this->couple_id,
                    'is_recurring' => true,
                    'recurring_interval' => $this->frequency,
                ]);

                $count++;
                $this->update(['last_generated_at' => $cursor->toDateString()]);
            }

            $cursor->addDay();
        }

        return $count;
    }
}
