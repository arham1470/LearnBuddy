<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('LearnBuddy')
            ->brandLogo(fn () => asset('images/logo.png'))
            ->brandLogoHeight('2.25rem')
            ->colors([
                'primary' => [
                    50 => '#fff7ed',
                    100 => '#ffedd5',
                    200 => '#fed7aa',
                    300 => '#fdba74',
                    400 => '#fb923c',
                    500 => '#FF5F00', // LearnBuddy Master Brand Orange
                    600 => '#ea580c',
                    700 => '#c2410c',
                    800 => '#9a3412',
                    900 => '#7c2d12',
                    950 => '#431407',
                ],
                'gray' => Color::Neutral,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Academic & Courses',
                'Campus & Batches',
                'Student Learning & Progress',
                'Assessments & Grading',
                'Financial & Orders',
                'User & Security',
                'Analytics & System',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->viteTheme('resources/css/app.css')
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn () => new \Illuminate\Support\HtmlString('<script>if(localStorage.getItem("lb_admin_sidebar_init")===null){localStorage.setItem("isOpen","true");localStorage.setItem("lb_admin_sidebar_init","1");}</script>'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.topbar.styles'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.sidebar.styles'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.widgets.dashboard-table-styles'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => view('filament.topbar.search'),
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.topbar.end'),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_END,
                fn () => view('filament.sidebar.promo'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
