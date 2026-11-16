<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\EditRecord;

class EditVideo extends EditRecord
{
    protected static string $resource = VideoResource::class;

    protected function afterSave(): void
    {
        AuditLog::log('video_lesson_updated', "Updated video settings for lesson: {$this->record->title}", $this->record);
    }
}
