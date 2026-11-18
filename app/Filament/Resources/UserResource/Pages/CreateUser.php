<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();
        $data['approved_at'] = now();
        $data['approved_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        AuditLog::log('user_created', "Created user account: {$this->record->email}", $this->record);
    }
}
