<?php

namespace App\Services;

use App\Filament\Company\Resources\ContractTemplateResource as ResourcesContractTemplateResource;
use App\Filament\Resources\ContractTemplateResource;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Contract;
use App\Settings\IntegrationsSettings;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Str;

class DocuSignService
{
    private string $baseUri;
    private string $oauthUri;
    private string $webbookurl;
    private string $redirectLink;
    private string $clientId;

    public function __construct()
    {
        $settings = app()->make(IntegrationsSettings::class)->docusign;
        $this->baseUri = (string)data_get($settings, 'base_uri', "https://demo.docusign.net");
        $this->oauthUri = (string)data_get($settings, 'oauth_uri', 'https://account-d.docusign.com/oauth/auth');
        $this->redirectLink = (string)data_get($settings, 'redirect_link', '#');
        $this->webbookurl = (string)data_get($settings, 'events_webhook_url', route('docusign.webhook'));
        $this->clientId = (string)data_get($settings, 'integration_key', '');
    }

    public function printPdf(Contract $contract, $code = null): mixed
    {
        if (@$contract->docusign_data?->signed_pdf) {
            return response()->file($contract->docusign_data?->signed_pdf);
        }
        if (!$code) {
            return $this->redirectOAuth($contract, 'printPdf');
        }
        $credentials = $this->getJWTToken($code);

        $accessToken = data_get($credentials, 'access_token');
        $accountId = data_get($credentials, 'account_id');
        $envelopeId = data_get($contract->docusign_data, 'envelopeId');

        $response = Http::withToken($accessToken)
            ->get("$this->baseUri/restapi/v2.1/accounts/{$accountId}/envelopes/{$envelopeId}/documents/combined");

        if ($response->failed()) abort(500, 'Erro ao baixar contrato assinado');
        $streamContent = $response->body();
        $path = $this->generatePdf($contract, $streamContent);
        $docusignData = $contract->docusign_data;
        $docusignData->signed_pdf = $path;
        $contract->docusign_data = $docusignData;
        $contract->save();
        return response()->file($path);
    }

    public function sendToSign(Contract $contract, $code = null): RedirectResponse
    {
        if (!$code) {
            return $this->redirectOAuth($contract, 'sendToSign');
        }
        $credentials = $this->getJWTToken($code);
        return $this->sendDocument($contract, $credentials);
    }

    private function sendDocument(Contract $contract, array $credentials): RedirectResponse
    {
        $documentPath = $this->generatePdf($contract);
        if (!file_exists($documentPath)) {
            throw new \Exception("File does not exist : $documentPath");
        }

        if (!filter_var($this->webbookurl, FILTER_VALIDATE_URL)) {
            throw new \Exception("Webhook URL inválida: {$this->webbookurl}");
        }

        $documentContents = file_get_contents($documentPath);
        $documentBase64 = base64_encode($documentContents);
        $filename = $contract->getFileName();

        $payload = [
            'emailSubject' => "Contrato " . $contract->enterprise->name,
            'documents' => [[
                'documentBase64' => $documentBase64,
                'name' => $filename,
                'fileExtension' => 'pdf',
                'documentId' => (string) $contract->id,
            ]],
            'recipients' => [
                'signers' => [[
                    'email' => $contract->customer->email,
                    'name' => $contract->customer->name,
                    'recipientId' => (string) $contract->customer->id,
                ]],
            ],
            'status' => 'sent',
            "eventNotification" => [
                "url" => $this->webbookurl,
                "loggingEnabled" => true,
                "requireAcknowledgment" => true,
                "useSoapInterface" => false,
                "includeCertificateOfCompletion" => true,
                "envelopeEvents" => [
                    ["envelopeEventStatusCode" => "sent"],
                    ["envelopeEventStatusCode" => "completed"],
                    ["envelopeEventStatusCode" => "declined"],
                ],
                "recipientEvents" => [
                    ["recipientEventStatusCode" => "Completed"],
                    ["recipientEventStatusCode" => "Declined"],
                ]
            ]
        ];

        $accessToken = data_get($credentials, 'access_token');
        $accountId = data_get($credentials, 'account_id');
        $envelopeResponse = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("$this->baseUri/restapi/v2.1/accounts/$accountId/envelopes", $payload);

        if ($envelopeResponse->failed()) {
            $status = $envelopeResponse->status();
            $body = $envelopeResponse->body();
            throw new \Exception("DocuSign error ($status): $body");
        }

        $result = $envelopeResponse->json();
        $result["status"] = strtolower(data_get($result, 'status', 'sent'));
        $envelopeId = data_get($result, 'envelopeId');
        $result["sign_url"] = $this->createSignLink($contract, $envelopeId, $credentials);
        $contract->docusign_data = $result;
        $contract->save();
        Notification::make()
            ->title('Contrato enviado com sucesso')
            ->success()
            ->send();

        return redirect()->route('filament.admin.resources.contracts.edit', [
            'record' => $contract->getKey(),
        ]);
    }

    public function createSignLink(Contract $contract, $envelopeId, array $credentials): string
    {
        $recipientViewRequest = [
            "authenticationMethod" => "email",
            "recipientId" => (string) $contract->customer->id,
            "userName" => $contract->customer->name,
            "email" => $contract->customer->email,
            "returnUrl" =>  $this->redirectLink ?: config("app.url")
        ];

        $accessToken = data_get($credentials, 'access_token');
        $accountId = data_get($credentials, 'account_id');

        $response = Http::withToken($accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("$this->baseUri/restapi/v2.1/accounts/$accountId/envelopes/$envelopeId/views/recipient", $recipientViewRequest);

        if ($response->failed()) {
            throw new \Exception("Erro ao gerar link de assinatura: " . $response->body());
        }

        $signUrl = $response->json('url');

        $docusignData = (array)$contract->docusign_data ?? [];
        $docusignData['sign_link'] = $signUrl;
        $contract->docusign_data = $docusignData;
        $contract->save();

        return $signUrl;
    }

    private function getJWTToken($code): array
    {
        try {
            $codeVerifier = session('docusign_code_verifier');
            $response = Http::asForm()->post("$this->oauthUri/oauth/token", [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => route('docusign.callback'),
                'client_id' => $this->clientId,
                'code_verifier' => $codeVerifier,
            ]);
            $credentials = $response->json();
            $accessToken = data_get($credentials, 'access_token');
            $response = Http::withToken($accessToken)->get("$this->oauthUri/oauth/userinfo");
            $accountId = $response->json('accounts.0.account_id');
            $credentials['account_id'] = $accountId;
            return $credentials;
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    private function redirectOAuth(Contract $contract, string $action): RedirectResponse
    {
        $redirectUri = route('docusign.callback');
        $codeVerifier = Str::random(64);

        $codeChallenge = rtrim(strtr(
            base64_encode(hash('sha256', $codeVerifier, true)),
            '+/',
            '-_'
        ), '=');

        session(['docusign_code_verifier' => $codeVerifier]);
        $authorizationUrl = "$this->oauthUri/oauth/auth?" . http_build_query([
            'response_type' => 'code',
            'scope' => 'signature',
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'state' => $contract->id . ',' . $action
        ]);
        return redirect($authorizationUrl);
    }

    public function generatePdf(Contract $contract, $pdfBinary = null): string
    {
        $filename = $contract->getFileName();

        if ($pdfBinary) {
            Storage::put('contracts/' . $filename, $pdfBinary);
        } else {
            $payload = $contract->contractable->getRenderPdfPayload();
            $html = $this->parseTemplate($contract->contractTemplate->content, $payload);
            $pdf = Pdf::loadHTML($html)->setPaper('a4');
            Storage::put('contracts/' . $filename, $pdf->output());
        }

        return Storage::path('contracts/' . $filename);
    }

    private function parseTemplate(string $template, $contractable): string
    {
        $replacements = [];
        foreach (ResourcesContractTemplateResource::$templateTags as $tag) {
            $cleanTag = str_replace('{{', '', str_replace('}}', '', $tag));
            $replacements[$tag] = data_get($contractable, $cleanTag, "");
        }
        return strtr($template, $replacements);
    }
}
