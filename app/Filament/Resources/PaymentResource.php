<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\AuditLog;
use App\Models\Payment;
use Filament\Actions\Action;
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

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Financial & Orders';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Transaction Overview')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('receipt_number')
                            ->label('Receipt #')
                            ->disabled(),

                        TextInput::make('transaction_reference')
                            ->label('Reference')
                            ->disabled(),

                        TextInput::make('gateway_payment_id')
                            ->label('Gateway ID')
                            ->disabled(),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->prefix(fn (Payment $record) => $record->currency ?? 'LKR')
                            ->disabled(),

                        TextInput::make('payment_gateway')
                            ->label('Provider')
                            ->disabled(),

                        TextInput::make('status')
                            ->disabled(),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('paid_at')
                            ->disabled(),

                        TextInput::make('refunded_at')
                            ->disabled(),
                    ]),

                    Textarea::make('refund_reason')
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('user.name')
                    ->label('Payer / Student')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('LKR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_SUCCESSFUL => 'success',
                        Payment::STATUS_PENDING => 'warning',
                        Payment::STATUS_REFUNDED => 'purple',
                        Payment::STATUS_FAILED, Payment::STATUS_CHARGEDBACK => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('paid_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Payment::STATUS_SUCCESSFUL => 'Successful',
                        Payment::STATUS_PENDING => 'Pending',
                        Payment::STATUS_REFUNDED => 'Refunded',
                        Payment::STATUS_FAILED => 'Failed',
                    ]),

                SelectFilter::make('payment_gateway')
                    ->options([
                        'stripe' => 'Stripe',
                        'payhere' => 'PayHere Sri Lanka',
                        'mock' => 'Mock Sandbox',
                    ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('refund')
                    ->label('Issue Refund')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record): bool => $record->status === Payment::STATUS_SUCCESSFUL)
                    ->form([
                        Textarea::make('reason')
                            ->label('Refund Justification')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        $record->update([
                            'status' => Payment::STATUS_REFUNDED,
                            'refund_amount' => $record->amount,
                            'refund_reason' => $data['reason'],
                            'refunded_at' => now(),
                        ]);

                        if ($record->order) {
                            $record->order->update(['status' => 'refunded']);
                        }

                        AuditLog::log('payment_refunded', "Refunded payment #{$record->id} ({$record->currency} {$record->amount}): {$data['reason']}", $record);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
