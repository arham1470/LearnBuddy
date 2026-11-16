<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function afterSave(): void
    {
        AuditLog::log('attendance_adjusted', "Adjusted attendance record #{$this->record->id} to {$this->record->status}", $this->record);
    }
}
