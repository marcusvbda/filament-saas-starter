<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Api\Requests\SendEmailRequest;
use App\Providers\MailServiceProvider;
use DB;
use Mail;

class SendEmailHandler extends Handlers
{
    public static string | null $uri = '/send-email';
    public static string | null $resource = UserResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }


    /**
     * Send FAQ email
     *
     * @param SendEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(SendEmailRequest $request)
    {
        try {
            $id = auth()->user()->id;
            $model = static::getModel()::find($id);

            if (!$model) {
                return static::sendNotFoundResponse();
            }

            MailServiceProvider::defineEmailConfig();
            $subject = $request->subject;
            $body = $request->body;
            $user = $model;

            $settingsPayload = DB::table("settings")->where(["group" => "integrations", "name" => "faq"])->first()?->payload ?? "{}";
            $settings = json_decode($settingsPayload, true);

            $bodyContent = data_get($settings, 'body');
            $bodyContent = str_replace('{{nome_usuario}}', $user->name, $bodyContent);
            $bodyContent = str_replace('{{email_usuario}}', $user->email, $bodyContent);
            $bodyContent = str_replace('{{motivo_do_contato}}', $subject, $bodyContent);
            $bodyContent = str_replace('{{mensagem}}', $body, $bodyContent);

            Mail::html($bodyContent, function ($message) use ($settings, $user) {
                $message->to(data_get($settings, 'to'))
                    ->subject(data_get($settings, 'subject'))
                    ->from($user->email, $user->name);
            });

            return static::sendSuccessResponse([], "Email sent successfully");
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erro ao enviar: ' . $th->getMessage(),
            ], 500);
        }
    }
}
