<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'tipe',
        'jumlah',
        'tanggal',
        'deskripsi',
        'peruntukan_id',
        'budget_id',
        'metode_pembayaran_id',
        'user_id',
        'couple_id',
        'lampiran',
        'is_recurring',
        'recurring_interval',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function peruntukan()
    {
        return $this->belongsTo(Category::class, 'peruntukan_id');
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(PaymentMethod::class, 'metode_pembayaran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
