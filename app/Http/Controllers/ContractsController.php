<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\DocuSignService;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class ContractsController extends Controller
{
    public function generateContract(Contract $contract)
    {
        try {
            return app(DocuSignService::class)->sendToSign($contract);
        } catch (Exception $e) {
            Notification::make()
                ->title("Erro ao enviar contrato para assinatura.")
                ->danger()
                ->send();
            return redirect()->back();
        }
    }

    public function printContract(Contract $contract)
    {
        try {
            return app(DocuSignService::class)->printPdf($contract);
        } catch (Exception $e) {
            Notification::make()
                ->title("Erro ao imprimir contrato.")
                ->danger()
                ->send();
            return redirect()->back();
        }
    }

    public function callback(Request $request)
    {
        $code = $request->code;
        $state = $request->state;
        if (!$code || !$state) return abort(404);
        $explode = explode(',', $state);
        [$contractId, $action] = $explode;
        $contract = Contract::findOrFail($contractId);
        return  app(DocuSignService::class)->{$action}($contract, $code);
    }

    public function webhookDocusign(Request $request)
    {
        $xml = simplexml_load_string($request->getContent());
        $json = json_encode($xml);
        $data = json_decode($json, true);

        $docusignId = data_get($data, 'EnvelopeStatus.EnvelopeID');
        $status = strtolower(data_get($data, 'EnvelopeStatus.Status', ''));
        $contract = Contract::where('docusign_data->envelopeId', $docusignId)->first();
        if ($contract) {
            $docusignData = $contract->docusign_data;
            $docusignData->status = $status;
            $contract->docusign_data = $docusignData;
            $contract->save();
        }

        return response('OK');
    }
}
