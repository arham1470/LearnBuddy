<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\AuditLog;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
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

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected static string | \UnitEnum | null $navigationGroup = 'Student Learning & Progress';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'course', 'deliveryOption', 'batch']);

        $user = auth()->user();

        // Instructors can only view enrollments belonging to courses they instruct
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Enrollment Information')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->label('Student'),

                        Select::make('course_id')
                            ->relationship('course', 'title')
                            ->disabled()
                            ->label('Course'),
                    ]),

                    Grid::make(3)->schema([
                        Select::make('status')
                            ->options([
                                Enrollment::STATUS_ACTIVE => 'Active',
                                Enrollment::STATUS_PENDING_PAYMENT => 'Pending Payment',
                                Enrollment::STATUS_CANCELLED => 'Cancelled',
                                Enrollment::STATUS_COMPLETED => 'Completed',
                                Enrollment::STATUS_EXPIRED => 'Expired',
                            ])
                            ->required(),

                        TextInput::make('price_paid')
                            ->numeric()
                            ->prefix('LKR')
                            ->disabled(),

                        TextInput::make('enrolled_at')
                            ->disabled(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('deliveryOption.delivery_mode')
                    ->label('Delivery Mode')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'physical' => 'warning',
                        'hybrid' => 'primary',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Online')),

                TextColumn::make('batch.batch_code')
                    ->label('Batch')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A (Online)'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Enrollment::STATUS_ACTIVE => 'success',
                        Enrollment::STATUS_PENDING_PAYMENT => 'warning',
                        Enrollment::STATUS_CANCELLED => 'danger',
                        Enrollment::STATUS_COMPLETED => 'purple',
                        default => 'gray',
                    }),

                TextColumn::make('enrolled_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Enrollment::STATUS_ACTIVE => 'Active',
                        Enrollment::STATUS_PENDING_PAYMENT => 'Pending Payment',
                        Enrollment::STATUS_CANCELLED => 'Cancelled',
                        Enrollment::STATUS_COMPLETED => 'Completed',
                    ]),

                SelectFilter::make('course')
                    ->relationship('course', 'title'),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('cancel_enrollment')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Enrollment $record): bool => auth()->user()?->isAdmin() && $record->status === Enrollment::STATUS_ACTIVE)
                    ->action(function (Enrollment $record): void {
                        $record->update(['status' => Enrollment::STATUS_CANCELLED]);
                        AuditLog::log('enrollment_cancelled', "Admin cancelled student enrollment #{$record->id} for {$record->user?->email}", $record);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
        ];
    }
}
