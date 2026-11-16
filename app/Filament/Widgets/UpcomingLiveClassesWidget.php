<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\LiveClass;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingLiveClassesWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();

        $query = LiveClass::query()
            ->with(['course', 'instructor'])
            ->whereIn('status', [LiveClass::STATUS_SCHEDULED, LiveClass::STATUS_LIVE])
            ->where('scheduled_start_time', '>=', now()->subHours(2))
            ->orderBy('scheduled_start_time', 'asc')
            ->limit(5);

        if (! $isAdmin) {
            $courseIds = Course::whereHas('instructors', fn ($q) => $q->where('users.id', $user->id))->pluck('id');
            $query->whereIn('course_id', $courseIds);
        }

        return $table
            ->heading('Upcoming & Ongoing Live Sessions')
            ->query($query)
            ->columns([
                TextColumn::make('title')
                    ->label('Session')
                    ->weight('bold')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->course?->title),

                TextColumn::make('instructor.name')
                    ->label('Lecturer'),

                TextColumn::make('scheduled_start_time')
                    ->label('Date & Time')
                    ->dateTime('M d, Y h:i A (T)'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        LiveClass::STATUS_LIVE => 'danger',
                        LiveClass::STATUS_SCHEDULED => 'primary',
                        LiveClass::STATUS_COMPLETED => 'success',
                        default => 'gray',
                    }),
            ])
            ->emptyStateHeading('No live classes')
            ->emptyStateDescription('There are no upcoming or ongoing sessions at the moment.')
            ->emptyStateIcon('heroicon-o-video-camera')
            ->paginated(false);
    }
}
