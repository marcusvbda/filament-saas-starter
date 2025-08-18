<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Api\Requests\SendForgotPasswordEmailRequest;
use App\Providers\MailServiceProvider;
use DB;
use Mail;

class SendForgotPasswordHandler extends Handlers
{
    public static string | null $uri = '/send-forgot-password-email';
    public static string | null $resource = UserResource::class;
    public static bool $public = true;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Send Forgot User Password Email
     *
     * @param SendForgotPasswordEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(SendForgotPasswordEmailRequest $request)
    {
        $model = static::getModel()::where("email", $request->email)->first();

        if (!$model) {
            return static::sendNotFoundResponse();
        }

        DB::table("password_reset_tokens")->where("email", $model->email)->delete();
        $token = strtoupper(substr(uniqid(), -5));
        DB::table("password_reset_tokens")->insert([
            "email" => $request->email,
            "token" => $token,
            "created_at" => now(),
            "due_date" => now()->addMinutes(15),
        ]);

        MailServiceProvider::defineEmailConfig();
        $bodyContent = <<<EOT
            Seu token de redefinição de senha é: <b>$token</b> e tem vencimento em 15 minutos.
        EOT;
        Mail::html($bodyContent, function ($message) use ($request, $token) {
            $message->to($request->email)
                ->subject("Redefinição de senha");
        });

        return static::sendSuccessResponse($request->email, "Email sent successfully");
    }
}
