<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'User & Security';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Account Credentials & Role')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText('Leave blank when editing to keep current password.'),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+94 77 123 4567'),
                    ]),

                    Select::make('roles')
                        ->relationship('roles', 'display_name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                ]),

            Section::make('Account Status & Regional Info')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('is_active')
                            ->label('Active Account')
                            ->default(true),

                        Toggle::make('is_approved')
                            ->label('Approved (Required for Instructor teaching access)')
                            ->default(true),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('city')
                            ->maxLength(100),

                        TextInput::make('district')
                            ->maxLength(100),

                        TextInput::make('province')
                            ->maxLength(100),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('roles.display_name')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'administrator', 'admin' => 'danger',
                        'instructor' => 'warning',
                        default => 'info',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),

                TextColumn::make('city')
                    ->label('Location')
                    ->state(fn (User $record): string => $record->city ? "{$record->city}, {$record->district}" : 'Sri Lanka')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'display_name'),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                SelectFilter::make('is_approved')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Pending Approval',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => ! $record->is_approved && $record->isInstructor())
                    ->action(function (User $record): void {
                        $record->update([
                            'is_approved' => true,
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        AuditLog::log('user_approved', "Approved instructor account: {$record->email}", $record);
                    }),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => auth()->user()?->id !== $record->id)
                    ->after(fn (User $record) => AuditLog::log('user_deleted', "Deleted user account: {$record->email}")),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
