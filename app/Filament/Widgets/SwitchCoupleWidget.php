<?php

namespace App\Filament\Widgets;

use App\Models\Couple;
use Filament\Widgets\Widget;
use Livewire\Attributes\Url;

class SwitchCoupleWidget extends Widget
{
    protected string $view = 'filament.widgets.switch-couple';

    public ?string $activeCoupleId = null;

    public function mount(): void
    {
        $this->activeCoupleId = session('active_couple_id');
    }

    public function getCouples(): array
    {
        return Couple::where('is_active', true)
            ->orderBy('nama')
            ->pluck('nama', 'id')
            ->toArray();
    }

    public function isActiveSuperadmin(): bool
    {
        return auth()->user()->role === 'superadmin';
    }

    public function updatedActiveCoupleId($value): void
    {
        session(['active_couple_id' => $value]);
        $this->dispatch('couple-switched');
    }
}
