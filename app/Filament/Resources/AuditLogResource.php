<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'User & Security';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?string $pluralModelLabel = 'Audit Logs';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Security & Action Audit Entry')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('action')
                            ->disabled(),

                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Timestamp')
                            ->disabled(),
                    ]),

                    TextInput::make('user.email')
                        ->label('Executed By User')
                        ->disabled(),

                    Textarea::make('description')
                        ->rows(2)
                        ->disabled(),

                    Textarea::make('user_agent')
                        ->rows(2)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp (SL Time)')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Actor')
                    ->searchable()
                    ->placeholder('System Automation'),

                TextColumn::make('action')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(45)
                    ->color('gray'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->fontFamily('mono')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'user_approved' => 'User Approved',
                        'user_created' => 'User Created',
                        'user_updated' => 'User Updated',
                        'user_deleted' => 'User Deleted',
                        'instructor_approved' => 'Instructor Approved',
                        'instructor_suspended' => 'Instructor Suspended',
                        'course_status_changed' => 'Course Status Changed',
                        'batch_created' => 'Batch Created',
                        'batch_cancelled' => 'Batch Cancelled',
                        'payment_refunded' => 'Payment Refunded',
                        'certificate_revoked' => 'Certificate Revoked',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
