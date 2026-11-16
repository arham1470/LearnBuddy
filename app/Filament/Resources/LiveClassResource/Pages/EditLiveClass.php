<?php

namespace App\Filament\Resources\LiveClassResource\Pages;

use App\Filament\Resources\LiveClassResource;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveClass extends EditRecord
{
    protected static string $resource = LiveClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation()->visible(fn () => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::log('live_class_updated', "Updated live class: {$this->record->title}", $this->record);
    }
}
