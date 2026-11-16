<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Filament\Actions\EditAction;
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

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'Analytics & System';

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $modelLabel = 'System Setting';

    protected static ?string $pluralModelLabel = 'System Settings';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Setting Configuration')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('key')
                            ->disabled()
                            ->required(),
                    ]),

                    Select::make('group')
                        ->options([
                            'general' => 'General Platform',
                            'localization' => 'Sri Lanka Localization (Currency & Timezone)',
                            'courses' => 'Course & Batch Defaults',
                            'billing' => 'Invoicing & Tax Regulations',
                            'providers' => 'External Services (Mux, Jitsi)',
                        ])
                        ->required(),

                    Textarea::make('value')
                        ->label('Configured Value')
                        ->rows(3)
                        ->required(),

                    Textarea::make('description')
                        ->rows(2)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('key')
                    ->fontFamily('mono')
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('value')
                    ->limit(40)
                    ->color('primary')
                    ->weight('semibold'),

                TextColumn::make('group')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'localization' => 'Localization',
                        'courses' => 'Courses',
                        'billing' => 'Billing',
                        'providers' => 'Providers',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
