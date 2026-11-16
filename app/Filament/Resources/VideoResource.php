<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Lesson;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
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

class VideoResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';

    protected static string | \UnitEnum | null $navigationGroup = 'Academic & Courses';

    protected static ?string $navigationLabel = 'Video Lessons';

    protected static ?string $modelLabel = 'Video Lesson';

    protected static ?string $pluralModelLabel = 'Video Lessons';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where(function (Builder $q) {
                $q->whereNotNull('video_playback_id')
                    ->orWhereNotNull('video_asset_id')
                    ->orWhereNotNull('video_upload_id')
                    ->orWhere('type', 'video');
            })
            ->with(['section.course']);

        $user = auth()->user();

        // Instructors can only view video lessons for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('section.course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Video Asset & Lesson Metadata')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('duration_minutes')
                            ->label('Duration (Minutes)')
                            ->numeric()
                            ->suffix('min'),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('video_provider')
                            ->label('Streaming Provider')
                            ->disabled(),

                        TextInput::make('video_status')
                            ->label('Transcode Status')
                            ->disabled(),

                        TextInput::make('video_playback_policy')
                            ->label('Playback Access Policy')
                            ->disabled(),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('video_playback_id')
                            ->label('Encrypted Playback ID')
                            ->disabled(),

                        TextInput::make('video_asset_id')
                            ->label('Provider Asset ID')
                            ->disabled(),
                    ]),

                    Grid::make(2)->schema([
                        Toggle::make('is_preview')
                            ->label('Free Public Preview Lesson'),

                        Toggle::make('is_published')
                            ->label('Published to Curriculum'),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Lesson Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('section.course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('section.title')
                    ->label('Module / Section')
                    ->color('gray')
                    ->limit(20),

                TextColumn::make('video_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'ready' => 'success',
                        'processing', 'waiting' => 'warning',
                        'errored', 'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Pending')),

                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),

                IconColumn::make('is_preview')
                    ->label('Free Preview')
                    ->boolean(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('video_status')
                    ->options([
                        'ready' => 'Ready / Active',
                        'processing' => 'Processing',
                        'errored' => 'Error / Failed',
                    ]),

                SelectFilter::make('is_preview')
                    ->options([
                        '1' => 'Free Preview',
                        '0' => 'Enrolled Only',
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
            'index' => Pages\ListVideos::route('/'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
