<?php

namespace App\Filament\Company\Resources\EventResource\Pages;

use App\Filament\Company\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->refreshEventPage();
    }

    #[On('refreshEventPage')]
    public function refreshEventPage(): void
    {
        $this->dispatch('refresh');
    }
}
