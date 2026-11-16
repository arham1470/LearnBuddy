<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Models\Assignment;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Assessments & Grading';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['course'])->withCount('submissions');

        $user = auth()->user();

        // Instructors can only view assignments for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Assignment Specifications')
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

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                    ]),

                    Textarea::make('description')
                        ->rows(3)
                        ->required(),

                    Textarea::make('instructions')
                        ->label('Submission Instructions & Requirements')
                        ->rows(3),

                    Grid::make(3)->schema([
                        TextInput::make('max_points')
                            ->label('Max Score')
                            ->numeric()
                            ->default(100)
                            ->required(),

                        TextInput::make('passing_points')
                            ->label('Passing Score')
                            ->numeric()
                            ->default(50)
                            ->required(),

                        DateTimePicker::make('due_date')
                            ->label('Deadline (Asia/Colombo)')
                            ->timezone('Asia/Colombo')
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        Toggle::make('allow_late_submissions')
                            ->label('Allow Late Submissions with Tag')
                            ->default(true),

                        Toggle::make('is_published')
                            ->label('Published to Enrolled Students')
                            ->default(true),
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

                TextColumn::make('due_date')
                    ->label('Due Date (SL Time)')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('max_points')
                    ->label('Marks')
                    ->state(fn (Assignment $record): string => "Pass: {$record->passing_points} / {$record->max_points}")
                    ->badge()
                    ->color('gray'),

                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->relationship('course', 'title'),

                SelectFilter::make('is_published')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
