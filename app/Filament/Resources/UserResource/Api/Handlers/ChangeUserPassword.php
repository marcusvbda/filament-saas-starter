<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Api\Requests\ChangeUserPasswordRequest;
use DB;

class ChangeUserPassword extends Handlers
{
    public static string | null $uri = '/change-user-password';
    public static string | null $resource = UserResource::class;
    public static bool $public = true;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Change User Password
     *
     * @param ChangeUserPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(ChangeUserPasswordRequest $request)
    {
        $model = static::getModel()::where("email", $request->email)->first();

        if (!$model) {
            return static::sendNotFoundResponse();
        }

        $token = DB::table("password_reset_tokens")->where("email", $model->email)
            ->where("token", $request->token)
            ->where("due_date", ">=", now())
            ->first();

        if (empty($token)) {
            return response()->json([
                'message' => "Invalid or expired token",
            ], 401);
        }

        $model->password = $request->password;
        $model->save();

        DB::table("password_reset_tokens")->where("email", $model->email)
            ->where("token", $request->token)
            ->delete();

        return static::sendSuccessResponse([], "Successfully Updated Resource");
    }
}
