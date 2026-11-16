<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Campus & Batches';

    protected static ?string $navigationLabel = 'Classroom Attendance';

    protected static ?string $modelLabel = 'Attendance Record';

    protected static ?string $pluralModelLabel = 'Attendance Records';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['student', 'session.batch.deliveryOption.course', 'recordedBy']);

        $user = auth()->user();

        // Instructors can only view attendance for their own physical batches
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('session.batch.deliveryOption.course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Student Attendance Details')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->relationship('student', 'name')
                            ->disabled()
                            ->label('Student'),

                        Select::make('status')
                            ->options([
                                Attendance::STATUS_PRESENT => 'Present',
                                Attendance::STATUS_LATE => 'Late',
                                Attendance::STATUS_EXCUSED => 'Excused',
                                Attendance::STATUS_ABSENT => 'Absent',
                            ])
                            ->required(),
                    ]),

                    Textarea::make('remarks')
                        ->placeholder('e.g. Arrived 15 minutes late due to traffic, or medical excuse provided.')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('session.batch.batch_code')
                    ->label('Batch')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('session.session_date')
                    ->label('Session Date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('session.topic')
                    ->label('Topic')
                    ->limit(20)
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Attendance::STATUS_PRESENT => 'success',
                        Attendance::STATUS_LATE => 'warning',
                        Attendance::STATUS_EXCUSED => 'purple',
                        Attendance::STATUS_ABSENT => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('remarks')
                    ->limit(25)
                    ->color('gray')
                    ->placeholder('None'),

                TextColumn::make('recordedBy.name')
                    ->label('Recorded By')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Attendance::STATUS_PRESENT => 'Present',
                        Attendance::STATUS_LATE => 'Late',
                        Attendance::STATUS_EXCUSED => 'Excused',
                        Attendance::STATUS_ABSENT => 'Absent',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
