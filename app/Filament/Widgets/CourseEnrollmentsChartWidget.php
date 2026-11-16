<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class CourseEnrollmentsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Enrollment Overview';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
        'xl' => 2,
    ];

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $month    = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[]   = Enrollment::whereYear('enrolled_at', $month->year)
                ->whereMonth('enrolled_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Enrollments',
                    'data'                 => $data,
                    'borderColor'          => '#FF5F00',
                    'backgroundColor'      => 'rgba(255, 95, 0, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'pointBackgroundColor' => '#FF5F00',
                    'pointBorderColor'     => '#fff',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 7,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'backgroundColor' => '#1A1A1A',
                    'titleColor'      => '#FFFFFF',
                    'bodyColor'       => '#FF5F00',
                    'borderColor'     => '#FF5F00',
                    'borderWidth'     => 1,
                    'padding'         => 10,
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'grid'        => ['color' => 'rgba(0,0,0,0.04)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
