<?php

namespace App\Filament\Resources\UserResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\UserResource;


class UserApiService extends ApiService
{
    protected static string | null $resource = UserResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\DetailHandler::class,
            Handlers\SendForgotPasswordHandler::class,
            Handlers\ChangeUserPassword::class,
            Handlers\SendEmailHandler::class
        ];
    }
}
