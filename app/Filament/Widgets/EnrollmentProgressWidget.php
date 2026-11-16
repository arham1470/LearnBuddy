<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Filament\Widgets\Widget;

class EnrollmentProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.enrollment-progress';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
        'xl' => 1,
    ];

    protected function getViewData(): array
    {
        $totalEnrollments = Enrollment::count();
        $totalCapacity    = max($totalEnrollments * 2, 10);
        $percentage       = $totalCapacity > 0
            ? round(($totalEnrollments / $totalCapacity) * 100)
            : 0;

        return [
            'totalEnrollments' => $totalEnrollments,
            'totalCapacity'    => $totalCapacity,
            'percentage'       => $percentage,
        ];
    }
}
