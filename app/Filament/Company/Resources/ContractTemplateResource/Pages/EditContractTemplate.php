<?php

namespace App\Filament\Company\Resources\ContractTemplateResource\Pages;

use App\Filament\Company\Resources\ContractTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContractTemplate extends EditRecord
{
    protected static string $resource = ContractTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
