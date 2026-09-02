<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasDefaultTenant, HasTenants
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
        'couple_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! in_array($this->role, ['superadmin', 'suami', 'istri']) || ! ($this->is_active ?? true)) {
            return false;
        }

        if ($panel->getId() === 'superadmin' && ! $this->isSuperadmin()) {
            return false;
        }

        return true;
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        if ($this->isSuperadmin()) {
            $coupleId = session('active_couple_id');

            if ($coupleId && ($couple = Couple::find($coupleId))) {
                return $couple;
            }

            return Couple::query()->first();
        }

        return $this->couple;
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->isSuperadmin()) {
            return Couple::query()->get();
        }

        return $this->couple_id ? collect([$this->couple]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        return $this->couple_id === (int) $tenant->getKey();
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }
}
