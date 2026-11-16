<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\AuditLog;
use App\Models\Certificate;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Student Learning & Progress';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'course']);

        $user = auth()->user();

        // Instructors can only view certificates for courses they teach
        if ($user && $user->isInstructor() && ! $user->isAdmin()) {
            $query->whereHas('course.instructors', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Certificate Credential')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('certificate_code')
                            ->disabled()
                            ->label('Credential Code'),

                        TextInput::make('verification_hash')
                            ->disabled()
                            ->label('Security Hash'),

                        DatePicker::make('issue_date')
                            ->disabled(),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->label('Recipient'),

                        Select::make('course_id')
                            ->relationship('course', 'title')
                            ->disabled()
                            ->label('Course'),
                    ]),

                    TextInput::make('final_grade')
                        ->disabled()
                        ->label('Final Academic Grade (%)'),

                    Textarea::make('revoked_reason')
                        ->disabled()
                        ->visible(fn (Certificate $record) => $record->is_revoked),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_code')
                    ->label('Code')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('user.name')
                    ->label('Recipient')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('issue_date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('final_grade')
                    ->label('Grade')
                    ->state(fn (Certificate $record): string => $record->final_grade ? "{$record->final_grade}%" : 'Pass')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Validity')
                    ->state(fn (Certificate $record): string => $record->is_revoked ? 'Revoked' : 'Authentic & Valid')
                    ->badge()
                    ->color(fn (Certificate $record): string => $record->is_revoked ? 'danger' : 'success'),
            ])
            ->filters([
                SelectFilter::make('is_revoked')
                    ->label('Validity')
                    ->options([
                        '0' => 'Valid & Active',
                        '1' => 'Revoked',
                    ]),

                SelectFilter::make('course')
                    ->relationship('course', 'title'),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('public_verification')
                    ->label('Verify')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Certificate $record): string => $record->verificationUrl())
                    ->openUrlInNewTab(),

                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Certificate $record): bool => auth()->user()?->isAdmin() && ! $record->is_revoked)
                    ->form([
                        Textarea::make('reason')
                            ->label('Official Revocation Reason')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Certificate $record, array $data): void {
                        $record->update([
                            'is_revoked' => true,
                            'revoked_reason' => $data['reason'],
                            'revoked_at' => now(),
                        ]);
                        AuditLog::log('certificate_revoked', "Revoked certificate {$record->certificate_code}: {$data['reason']}", $record);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'view' => Pages\ViewCertificate::route('/{record}'),
        ];
    }
}
