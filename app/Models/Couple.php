<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Couple extends Model implements HasCurrentTenantLabel, HasName
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFilamentName(): string
    {
        return $this->nama;
    }

    public function getCurrentTenantLabel(): string
    {
        return $this->nama;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }
}
