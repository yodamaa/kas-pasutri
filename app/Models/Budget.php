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
        'jumlah',
        'bulan',
        'tahun',
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
}
