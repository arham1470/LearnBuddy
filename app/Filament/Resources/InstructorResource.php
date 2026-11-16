<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstructorResource\Pages;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';

    protected static string | \UnitEnum | null $navigationGroup = 'User & Security';

    protected static ?string $navigationLabel = 'Instructors';

    protected static ?string $modelLabel = 'Instructor';

    protected static ?string $pluralModelLabel = 'Instructors';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))
            ->withCount('taughtCourses');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Instructor Profile & Credentials')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->placeholder('+94 77 123 4567'),

                        TextInput::make('city')
                            ->placeholder('e.g. Colombo, Kandy'),
                    ]),

                    Textarea::make('bio')
                        ->label('Professional Bio / Academic Background')
                        ->rows(4),

                    Grid::make(2)->schema([
                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->default(true),

                        Toggle::make('is_approved')
                            ->label('Approved to Teach on LearnBuddy')
                            ->helperText('Unapproved instructors cannot access course management consoles.')
                            ->default(true),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Photo')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('taught_courses_count')
                    ->label('Assigned Courses')
                    ->counts('taughtCourses')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('city')
                    ->label('Location')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Pending Approval',
                    ]),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Suspended',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => ! $record->is_approved)
                    ->action(function (User $record): void {
                        $record->update([
                            'is_approved' => true,
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        AuditLog::log('instructor_approved', "Approved instructor: {$record->email}", $record);
                    }),

                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => (bool) $record->is_active)
                    ->action(function (User $record): void {
                        $record->update(['is_active' => false]);
                        AuditLog::log('instructor_suspended', "Suspended instructor: {$record->email}", $record);
                    }),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->after(fn (User $record) => AuditLog::log('instructor_deleted', "Deleted instructor profile: {$record->email}")),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstructors::route('/'),
            'edit' => Pages\EditInstructor::route('/{record}/edit'),
        ];
    }
}
