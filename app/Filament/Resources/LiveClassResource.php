<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveClassResource\Pages;
use App\Models\AuditLog;
use App\Models\LiveClass;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LiveClassResource extends Resource
{
    protected static ?string $model = LiveClass::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';

    protected static string | \UnitEnum | null $navigationGroup = 'Student Learning & Progress';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['course', 'instructor']);

        $user = auth()->user();

        // Instructors can only view/manage live classes for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('instructor_id', $user->id)
                    ->orWhereHas('course.instructors', fn ($iq) => $iq->where('users.id', $user->id));
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Live Class Scheduling (Asia/Colombo)')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('course_id')
                            ->relationship('course', 'title', function (Builder $query) {
                                $user = auth()->user();
                                if ($user && $user->isInstructor() && ! $user->isAdmin()) {
                                    $query->whereHas('instructors', fn ($q) => $q->where('users.id', $user->id));
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('instructor_id')
                            ->relationship('instructor', 'name', function (Builder $query) {
                                $query->whereHas('roles', fn ($q) => $q->where('name', 'instructor'));
                            })
                            ->searchable()
                            ->default(auth()->id())
                            ->required(),
                    ]),

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->rows(3),

                    Grid::make(2)->schema([
                        DateTimePicker::make('scheduled_start_time')
                            ->label('Start Date & Time (SL GMT+5:30)')
                            ->timezone('Asia/Colombo')
                            ->required(),

                        DateTimePicker::make('scheduled_end_time')
                            ->label('End Date & Time (SL GMT+5:30)')
                            ->timezone('Asia/Colombo')
                            ->required(),
                    ]),

                    Grid::make(3)->schema([
                        Select::make('provider')
                            ->options([
                                'jitsi' => 'Jitsi Meet (Virtual Classroom)',
                            ])
                            ->default('jitsi')
                            ->required(),

                        Select::make('status')
                            ->options([
                                LiveClass::STATUS_SCHEDULED => 'Scheduled',
                                LiveClass::STATUS_LIVE => 'Live In Session',
                                LiveClass::STATUS_COMPLETED => 'Completed',
                                LiveClass::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(LiveClass::STATUS_SCHEDULED)
                            ->required(),

                        Select::make('access_permission')
                            ->options([
                                LiveClass::ACCESS_ENROLLED_STUDENTS => 'Enrolled Course Students',
                                LiveClass::ACCESS_BATCH_STUDENTS_ONLY => 'Specific Batch Only',
                                LiveClass::ACCESS_INSTRUCTORS_ONLY => 'Instructors Only',
                            ])
                            ->default(LiveClass::ACCESS_ENROLLED_STUDENTS)
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('course.title')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->color('gray'),

                TextColumn::make('scheduled_start_time')
                    ->label('Schedule (SL Time)')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        LiveClass::STATUS_LIVE => 'success',
                        LiveClass::STATUS_SCHEDULED => 'primary',
                        LiveClass::STATUS_COMPLETED => 'gray',
                        LiveClass::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        LiveClass::STATUS_SCHEDULED => 'Scheduled',
                        LiveClass::STATUS_LIVE => 'Live',
                        LiveClass::STATUS_COMPLETED => 'Completed',
                        LiveClass::STATUS_CANCELLED => 'Cancelled',
                    ]),

                SelectFilter::make('course')
                    ->relationship('course', 'title'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('cancel_class')
                    ->label('Cancel Session')
                    ->icon('heroicon-o-ban')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (LiveClass $record): bool => in_array($record->status, [LiveClass::STATUS_SCHEDULED, LiveClass::STATUS_LIVE]))
                    ->form([
                        TextInput::make('reason')
                            ->label('Cancellation Reason')
                            ->required(),
                    ])
                    ->action(function (LiveClass $record, array $data): void {
                        $record->cancel($data['reason']);
                        AuditLog::log('live_class_cancelled', "Cancelled live class '{$record->title}' for {$record->course?->title}: {$data['reason']}", $record);
                    }),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLiveClasses::route('/'),
            'create' => Pages\CreateLiveClass::route('/create'),
            'edit' => Pages\EditLiveClass::route('/{record}/edit'),
        ];
    }
}
