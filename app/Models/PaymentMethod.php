<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'icon',
        'warna',
        'is_active',
        'couple_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'metode_pembayaran_id');
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
