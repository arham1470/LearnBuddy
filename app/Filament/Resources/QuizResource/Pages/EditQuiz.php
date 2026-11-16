<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuiz extends EditRecord
{
    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation()->visible(fn () => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function afterSave(): void
    {
        AuditLog::log('quiz_updated', "Updated quiz: {$this->record->title}", $this->record);
    }
}
