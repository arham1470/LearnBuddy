<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->visible(fn () => auth()->user()?->id !== $this->record->id),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::log('user_updated', "Updated user account: {$this->record->email}", $this->record);
    }
}
