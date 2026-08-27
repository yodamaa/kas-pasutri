<?php

namespace App\Models;

use App\Traits\CoupleScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, CoupleScoped;

    protected $fillable = [
        'nama',
        'tipe',
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
        return $this->hasMany(Transaction::class, 'peruntukan_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'peruntukan_id');
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
