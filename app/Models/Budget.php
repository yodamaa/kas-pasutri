<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\CoupleScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory, LogsActivity, CoupleScoped;

    protected $fillable = [
        'peruntukan_id',
        'jumlah',
        'bulan',
        'tahun',
        'couple_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
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
}
