<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $roles = Role::whereIn("id", is_array($data['roles']) ? $data['roles'] : [$data['roles']]);
        $user = static::getModel()::create($data);
        if ($roles->exists()) $user->syncRoles($roles->pluck('name')->toArray());
        return $user;
    }
}
