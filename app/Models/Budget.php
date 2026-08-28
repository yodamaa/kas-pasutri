<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'peruntukan_id',
        'nama',
        'jumlah',
        'alert_threshold',
        'bulan',
        'tahun',
        'couple_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    public function peruntukan()
    {
        return $this->belongsTo(Category::class, 'peruntukan_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function getTerpakaiAttribute(): float
    {
        $otherBudgetExists = self::where('couple_id', $this->couple_id)
            ->where('peruntukan_id', $this->peruntukan_id)
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->where('id', '!=', $this->id)
            ->exists();

        $query = Transaction::where('tipe', 'pengeluaran')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($otherBudgetExists) {
                $q->where('budget_id', $this->id);
                if (! $otherBudgetExists) {
                    $q->orWhere(function ($q2) {
                        $q2->whereNull('budget_id')
                            ->where('peruntukan_id', $this->peruntukan_id);
                    });
                }
            })
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun);

        if ($this->couple_id) {
            $query->where('couple_id', $this->couple_id);
        }

        return (float) $query->sum('jumlah');
    }

    public function getSisaAttribute(): float
    {
        return max($this->jumlah - $this->terpakai, 0);
    }

    public function getPersentaseAttribute(): float
    {
        return $this->jumlah > 0 ? round(($this->terpakai / $this->jumlah) * 100, 1) : 0;
    }

    public function isThresholdExceeded(): bool
    {
        return $this->persentase >= $this->alert_threshold && $this->persentase < 100;
    }

    public function isExceeded(): bool
    {
        return $this->persentase >= 100;
    }
}
