<?php

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected function afterSave(): void
    {
        AuditLog::log('settings_updated', "Updated system setting: {$this->record->key} = {$this->record->value}", $this->record);
    }
}
