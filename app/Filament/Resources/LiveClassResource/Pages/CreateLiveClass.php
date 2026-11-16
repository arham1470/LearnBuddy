<?php

namespace App\Filament\Resources\LiveClassResource\Pages;

use App\Filament\Resources\LiveClassResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLiveClass extends CreateRecord
{
    protected static string $resource = LiveClassResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['meeting_identifier'] = 'lb-live-'.Str::random(12);

        return $data;
    }

    protected function afterCreate(): void
    {
        AuditLog::log('live_class_scheduled', "Scheduled live class '{$this->record->title}' for {$this->record->course?->title}", $this->record);
    }
}
