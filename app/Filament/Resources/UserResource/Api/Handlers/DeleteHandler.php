<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use DB;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\UserResource;

class DeleteHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = UserResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Delete User
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler()
    {
        $id = auth()->user()->id;

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        DB::transaction(function () use ($model) {
            $model->addresses()->delete();
            $model->phones()->delete();
            $model->delete();
        });

        return static::sendSuccessResponse($model, "Successfully Delete Resource");
    }
}
