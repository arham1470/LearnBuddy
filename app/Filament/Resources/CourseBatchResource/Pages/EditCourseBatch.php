<?php

namespace App\Filament\Resources\CourseBatchResource\Pages;

use App\Filament\Resources\CourseBatchResource;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseBatch extends EditRecord
{
    protected static string $resource = CourseBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation()->visible(fn () => auth()->user()?->isAdmin() && $this->record->enrolled_seats === 0),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::log('batch_updated', "Updated batch {$this->record->batch_code}", $this->record);
    }
}
