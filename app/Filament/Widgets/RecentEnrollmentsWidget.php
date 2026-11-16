<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentEnrollmentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

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
            ->headerActions([
                Action::make('viewAll')
                    ->label('View All')
                    ->url(fn (): string => EnrollmentResource::getUrl('index'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                    ->color('primary')
                    ->size('sm'),
            ])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->html()
                    ->formatStateUsing(function ($state, Enrollment $record): string {
                        $name = e($state ?? 'Unknown Student');
                        $email = e($record->user?->email ?? '');
                        $words = preg_split('/\s+/', trim($name));
                        $initials = '';
                        if (! empty($words[0])) {
                            $initials .= mb_substr($words[0], 0, 1);
                        }
                        if (isset($words[1]) && ! empty($words[1])) {
                            $initials .= mb_substr($words[1], 0, 1);
                        }
                        if (empty($initials)) {
                            $initials = 'ST';
                        }
                        $initials = strtoupper($initials);

                        return '<div class="lb-student-cell">
                            <div class="lb-student-avatar">' . $initials . '</div>
                            <div class="lb-student-info">
                                <span class="lb-student-name">' . $name . '</span>
                                ' . ($email ? '<span class="lb-student-email">' . $email . '</span>' : '') . '
                            </div>
                        </div>';
                    })
                    ->searchable(false),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->limit(35)
                    ->tooltip(fn (Enrollment $record): ?string => $record->course?->title)
                    ->weight('semibold')
                    ->color('gray'),

                TextColumn::make('deliveryOption.delivery_mode')
                    ->label('Mode')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower($state ?? 'online')) {
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
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Enrollment::STATUS_ACTIVE => 'Active',
                        Enrollment::STATUS_PENDING_PAYMENT => 'Pending Payment',
                        Enrollment::STATUS_CANCELLED => 'Cancelled',
                        default => ucfirst($state),
                    }),

                TextColumn::make('enrolled_at')
                    ->label('Enrolled On')
                    ->dateTime('M d, Y h:i A')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
