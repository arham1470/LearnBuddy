<?php

namespace App\Filament\Resources\CourseBatchResource\Pages;

use App\Filament\Resources\CourseBatchResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseBatch extends CreateRecord
{
    protected static string $resource = CourseBatchResource::class;

    protected function afterCreate(): void
    {
        AuditLog::log('batch_created', "Created batch {$this->record->batch_code}", $this->record);
    }
}
