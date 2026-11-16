<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CourseEnrollmentsChartWidget;
use App\Filament\Widgets\DashboardStatsOverviewWidget;
use App\Filament\Widgets\EnrollmentProgressWidget;
use App\Filament\Widgets\RecentEnrollmentsWidget;
use App\Filament\Widgets\UpcomingLiveClassesWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public function getHeader(): ?View
    {
        return view('filament.pages.dashboard-header');
    }

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverviewWidget::class,
            CourseEnrollmentsChartWidget::class,
            EnrollmentProgressWidget::class,
            RecentEnrollmentsWidget::class,
            UpcomingLiveClassesWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
