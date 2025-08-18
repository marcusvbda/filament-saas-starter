<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Api\Requests\UpdateUserRequest;
use App\Filament\Resources\UserResource\Api\Transformers\UserTransformer;
use DB;

class UpdateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = UserResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }


    /**
     * Update User
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateUserRequest $request)
    {
        try {
            $id = auth()->user()->id;

            $model = static::getModel()::find($id);

            if (!$model) {
                return static::sendNotFoundResponse();
            }

            DB::transaction(function () use ($request, $model) {
                $model->fill($request->all());
                $model->save();
                $model->syncMorphsMany('phones', $request->phones);
                $model->syncMorphsMany('addresses', $request->addresses);
            });

            return static::sendSuccessResponse(new UserTransformer($model), "Successfully Updated Resource");
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erro ao atualizar: ' . $th->getMessage(),
            ], 500);
        }
    }
}
