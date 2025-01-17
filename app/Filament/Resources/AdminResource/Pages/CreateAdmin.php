<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the 'type' field to 'admin' before creating the record
        $data['type'] = 'admin';
        return $data;
    }

    protected function afterCreate(): void
    {
        // Assign roles to the user after creation
        $roles = Role::whereIn('id', $this->data['roles'])->get();
        $this->record->syncRoles($roles);
    }
}
