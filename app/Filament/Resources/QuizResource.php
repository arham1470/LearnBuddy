<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Models\AuditLog;
use App\Models\Quiz;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Assessments & Grading';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['course'])->withCount('attempts');

        $user = auth()->user();

        // Instructors can only view quizzes for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Quiz Parameters')
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
                        ->rows(3),

                    Grid::make(3)->schema([
                        TextInput::make('passing_percentage')
                            ->label('Pass Mark (%)')
                            ->numeric()
                            ->default(70)
                            ->suffix('%')
                            ->required(),

                        TextInput::make('time_limit_minutes')
                            ->label('Time Limit (Minutes)')
                            ->numeric()
                            ->default(30)
                            ->suffix('min')
                            ->required(),

                        TextInput::make('max_attempts')
                            ->label('Max Attempts Allowed')
                            ->numeric()
                            ->default(3)
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        Toggle::make('show_correct_answers_after_attempt')
                            ->label('Show Correct Answers Post-Attempt')
                            ->default(true),

                        Toggle::make('is_published')
                            ->label('Published to Course Students')
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

                TextColumn::make('passing_percentage')
                    ->label('Pass Mark')
                    ->suffix('%')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('time_limit_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('attempts_count')
                    ->label('Attempts')
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
