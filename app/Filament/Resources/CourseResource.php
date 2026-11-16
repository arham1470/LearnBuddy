<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Academic & Courses';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['category', 'instructors', 'deliveryOptions']);

        $user = auth()->user();

        // Instructors can only view/manage their assigned courses
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Course Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->required()
                            ->unique(Course::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                    ]),

                    TextInput::make('subtitle')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->rows(4)
                        ->required(),

                    Grid::make(3)->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('instructors')
                            ->relationship('instructors', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('status')
                            ->options([
                                Course::STATUS_DRAFT => 'Draft',
                                Course::STATUS_REVIEW => 'Under Review',
                                Course::STATUS_PUBLISHED => 'Published',
                                Course::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->required()
                            ->default(Course::STATUS_DRAFT),
                    ]),

                    Grid::make(3)->schema([
                        Select::make('difficulty')
                            ->options([
                                'beginner' => 'Beginner',
                                'intermediate' => 'Intermediate',
                                'advanced' => 'Advanced',
                            ])
                            ->default('beginner')
                            ->required(),

                        Select::make('language')
                            ->options([
                                'English' => 'English',
                                'Sinhala' => 'Sinhala',
                                'Tamil' => 'Tamil',
                                'Bilingual (English / Sinhala)' => 'Bilingual (English / Sinhala)',
                            ])
                            ->default('English')
                            ->required(),

                        TextInput::make('duration_hours')
                            ->numeric()
                            ->suffix('hours'),
                    ]),

                    Toggle::make('is_featured')
                        ->label('Feature on Homepage Carousel'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->circular(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(35),

                TextColumn::make('category.name')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('instructors.name')
                    ->badge()
                    ->color('warning')
                    ->listWithLineBreaks()
                    ->limitList(2),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Course::STATUS_PUBLISHED => 'success',
                        Course::STATUS_REVIEW => 'warning',
                        Course::STATUS_DRAFT => 'gray',
                        Course::STATUS_ARCHIVED => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('price_range')
                    ->label('Price (LKR)')
                    ->state(fn (Course $record): string => $record->formattedPriceRange()),

                TextColumn::make('enrollments_count')
                    ->counts('enrollments')
                    ->label('Enrolled')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Course::STATUS_DRAFT => 'Draft',
                        Course::STATUS_REVIEW => 'Under Review',
                        Course::STATUS_PUBLISHED => 'Published',
                        Course::STATUS_ARCHIVED => 'Archived',
                    ]),

                SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
