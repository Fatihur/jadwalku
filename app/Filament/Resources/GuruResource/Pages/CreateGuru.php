<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function afterCreate(): void
    {
        // Assign role 'guru' to the user
        $this->record->user->assignRole('guru');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
