<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Superadmin\Pages\SuperadminDashboard;
use App\Filament\Superadmin\Resources\ActivityLogResource;
use App\Filament\Superadmin\Resources\CoupleResource;
use App\Filament\Superadmin\Resources\UserResource;
use App\Filament\Superadmin\Widgets\SuperadminCategoryChart;
use App\Filament\Superadmin\Widgets\SuperadminComparisonChart;
use App\Filament\Superadmin\Widgets\SuperadminCouplesWidget;
use App\Filament\Superadmin\Widgets\SuperadminRecentTransactionsWidget;
use App\Filament\Superadmin\Widgets\SuperadminStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('superadmin')
            ->login()
            ->brandName('Uang Pasutri • Superadmin')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->darkMode()
            ->topbar(true)
            ->sidebarCollapsibleOnDesktop()
            ->profile(EditProfile::class)
            ->resources([
                CoupleResource::class,
                UserResource::class,
                ActivityLogResource::class,
            ])
            ->pages([
                SuperadminDashboard::class,
            ])
            ->widgets([
                SuperadminStatsWidget::class,
                SuperadminComparisonChart::class,
                SuperadminCategoryChart::class,
                SuperadminRecentTransactionsWidget::class,
                SuperadminCouplesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
