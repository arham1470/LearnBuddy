<?php

namespace App\Filament\Resources\AssignmentResource\Pages;

use App\Filament\Resources\AssignmentResource;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssignment extends EditRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation()->visible(fn () => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::log('assignment_updated', "Updated assignment: {$this->record->title}", $this->record);
    }
}
