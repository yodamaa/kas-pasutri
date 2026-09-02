<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperadmin') && $user->isSuperadmin()) {
            return redirect()->to('/superadmin');
        }

        return redirect()->intended(Filament::getUrl());
    }
}
