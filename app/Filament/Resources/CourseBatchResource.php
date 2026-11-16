<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseBatchResource\Pages;
use App\Models\AuditLog;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\PhysicalLocation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class CourseBatchResource extends Resource
{
    protected static ?string $model = CourseBatch::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Campus & Batches';

    protected static ?string $navigationLabel = 'Campus Batches';

    protected static ?string $modelLabel = 'Campus Batch';

    protected static ?string $pluralModelLabel = 'Campus Batches';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['location', 'deliveryOption.course']);

        $user = auth()->user();

        // Instructors can only view batches for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('deliveryOption.course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Batch & Intake Configuration')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('course_delivery_option_id')
                            ->label('Course Physical / Hybrid Delivery Option')
                            ->relationship('deliveryOption', 'title', function (Builder $query) {
                                $query->whereIn('delivery_mode', ['physical', 'hybrid']);
                                $user = auth()->user();
                                if ($user && $user->isInstructor() && ! $user->isAdmin()) {
                                    $query->whereHas('course.instructors', fn ($q) => $q->where('users.id', $user->id));
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('location_id')
                            ->label('Campus Training Centre')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('batch_code')
                            ->required()
                            ->default(fn () => 'BAT-'.strtoupper(Str::random(6)))
                            ->unique(CourseBatch::class, 'batch_code', ignoreRecord: true),

                        TextInput::make('batch_name')
                            ->placeholder('e.g. Weekend Intake 01 - Colombo Campus')
                            ->required()
                            ->maxLength(255),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('max_seats')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->helperText('Strict room capacity limit to prevent physical overbooking.'),

                        Select::make('status')
                            ->options([
                                CourseBatch::STATUS_UPCOMING => 'Upcoming Intake',
                                CourseBatch::STATUS_OPEN => 'Open for Enrollment',
                                CourseBatch::STATUS_IN_PROGRESS => 'In Progress (Classes Running)',
                                CourseBatch::STATUS_COMPLETED => 'Completed Cohort',
                                CourseBatch::STATUS_CANCELLED => 'Cancelled Intake',
                            ])
                            ->default(CourseBatch::STATUS_OPEN)
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make('start_date')
                            ->label('Course Commence Date')
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('Course End Date')
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_code')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('batch_name')
                    ->searchable()
                    ->limit(25)
                    ->weight('semibold'),

                TextColumn::make('deliveryOption.course.title')
                    ->label('Course')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('location.name')
                    ->label('Campus')
                    ->badge()
                    ->color('warning')
                    ->placeholder('None'),

                TextColumn::make('enrolled_seats')
                    ->label('Seats')
                    ->state(fn (CourseBatch $record): string => "{$record->enrolled_seats} / {$record->max_seats}")
                    ->badge()
                    ->color(fn (CourseBatch $record): string => $record->isFull() ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        CourseBatch::STATUS_OPEN => 'success',
                        CourseBatch::STATUS_IN_PROGRESS => 'primary',
                        CourseBatch::STATUS_UPCOMING => 'warning',
                        CourseBatch::STATUS_COMPLETED => 'purple',
                        CourseBatch::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('start_date')
                    ->label('Start')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        CourseBatch::STATUS_OPEN => 'Open for Enrollment',
                        CourseBatch::STATUS_IN_PROGRESS => 'In Progress',
                        CourseBatch::STATUS_UPCOMING => 'Upcoming',
                        CourseBatch::STATUS_COMPLETED => 'Completed',
                        CourseBatch::STATUS_CANCELLED => 'Cancelled',
                    ]),

                SelectFilter::make('location')
                    ->relationship('location', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('cancel_batch')
                    ->label('Cancel Batch')
                    ->icon('heroicon-o-ban')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CourseBatch $record): bool => ! $record->isCancelled())
                    ->form([
                        TextInput::make('reason')
                            ->label('Cancellation Reason')
                            ->required(),
                    ])
                    ->action(function (CourseBatch $record, array $data): void {
                        $record->cancel(auth()->user(), $data['reason']);
                        AuditLog::log('batch_cancelled', "Cancelled physical batch {$record->batch_code}: {$data['reason']}", $record);
                    }),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (CourseBatch $record): bool => auth()->user()?->isAdmin() && $record->enrolled_seats === 0),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseBatches::route('/'),
            'create' => Pages\CreateCourseBatch::route('/create'),
            'edit' => Pages\EditCourseBatch::route('/{record}/edit'),
        ];
    }
}
