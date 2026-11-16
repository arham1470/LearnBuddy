<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Enrollment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentEnrollmentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();

        $query = Enrollment::query()
            ->with(['user', 'course', 'deliveryOption'])
            ->latest('enrolled_at')
            ->limit(5);

        if (! $isAdmin) {
            $courseIds = Course::whereHas('instructors', fn ($q) => $q->where('users.id', $user->id))->pluck('id');
            $query->whereIn('course_id', $courseIds);
        }

        return $table
            ->heading('Recent Course Enrollments')
            ->query($query)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->weight('bold'),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->limit(35),

                TextColumn::make('deliveryOption.delivery_mode')
                    ->label('Mode')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'physical' => 'warning',
                        'hybrid' => 'primary',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Online')),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Enrollment::STATUS_ACTIVE => 'success',
                        Enrollment::STATUS_PENDING_PAYMENT => 'warning',
                        Enrollment::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('enrolled_at')
                    ->label('Enrolled On')
                    ->dateTime('M d, Y h:i A'),
            ])
            ->paginated(false);
    }
}
