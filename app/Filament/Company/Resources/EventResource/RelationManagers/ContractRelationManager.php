<?php

namespace App\Filament\Company\Resources\EventResource\RelationManagers;

use App\Filament\Company\Resources\ContractTemplateResource;
use App\Models\ContractTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContractRelationManager extends RelationManager
{
    protected static string $relationship = 'contract';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Contracts');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
            Forms\Components\Select::make("contract_template_id")
                ->label(__("Template"))
                ->relationship('contractTemplate', 'name')
                ->searchable()
                ->createOptionForm(ContractTemplateResource::getFormFields()),
            Forms\Components\Group::make()
                ->label(__('Additional fields'))
                ->schema(
                    fn(Get $get) =>
                    collect(
                        ContractTemplate::find($get('contract_template_id'))
                            ?->additionalFields()
                            ->get() ?? []
                    )->map(function ($field) {
                        switch ($field->data['type']) {
                            case 'text':
                                return Forms\Components\TextInput::make("additional_data.{$field->data['key']}")
                                    ->label($field->data['label'])
                                    ->required($field->data['required'] ?? false)
                                    ->helperText($field->data['hint'] ?? null)
                                    ->default($field->data['default_value'] ?? null);
                            case 'select':
                                return Forms\Components\Select::make("additional_data.{$field->data['key']}")
                                    ->label($field->data['label'])
                                    ->options(collect($field->data['options'] ?? [])->mapWithKeys(fn($o) => [$o => $o]))
                                    ->multiple($field->data['multiple'] ?? false)
                                    ->required($field->data['required'] ?? false)
                                    ->helperText($field->data['hint'] ?? null)
                                    ->default($field->data['default_value'] ?? null);
                            case 'checkbox':
                                return Forms\Components\Toggle::make("additional_data.{$field->data['key']}")
                                    ->label($field->data['label'])
                                    ->required($field->data['required'] ?? false)
                                    ->helperText($field->data['hint'] ?? null)
                                    ->default($field->data['default_value'] ?? null);
                            default:
                                return Forms\Components\TextInput::make("additional_data.{$field->data['key']}")
                                    ->label($field->data['label'])
                                    ->required($field->data['required'] ?? false)
                                    ->helperText($field->data['hint'] ?? null)
                                    ->default($field->data['default_value'] ?? null);
                        }
                    })->toArray()
                )
                ->reactive(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())->columns(1);
    }

    protected function canCreate(): bool
    {
        return $this->ownerRecord->contract === null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->label(__("Name")),
                Tables\Columns\TextColumn::make('contractTemplate.name')->label(__("Template")),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/Y - H:i')->label(__("Created at")),
                Tables\Columns\TextColumn::make('updated_at')->date('d/m/Y - H:i')->label(__("Updated at"))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('Create contract'))->modalHeading(__('Create contract'))
                    ->after(fn($livewire) =>    $livewire->dispatch('refreshEventPage')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('sendUrlToFill')
                        ->label(__("Send fill url"))
                        ->color("success")
                        ->icon('heroicon-o-at-symbol')
                        ->requiresConfirmation()
                        ->modalHeading(__("Confirmation"))
                        ->modalDescription(__("Are you sure you want to send this contract fill url?"))
                        ->action(function ($record, $livewire) {
                            return $livewire->redirect(route('event.generate_url_to_fill', $record));
                        })
                        ->visible(fn($record) => !empty($record?->contractable?->customer) && empty($record?->contractable?->eventFillUrl)),
                    Tables\Actions\Action::make('deleteUrlToFill')
                        ->label(__("Delete fill url"))
                        ->color("danger")
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->modalHeading(__("Confirmation"))
                        ->modalDescription(__("Are you sure you want to delete this contract fill url?"))
                        ->action(function ($record, $livewire) {
                            $record?->contractable?->eventFillUrl()->delete();
                            $livewire->dispatch("refreshEventPage");
                        })
                        ->visible(fn($record) => !empty($record?->contractable?->eventFillUrl)),
                    Tables\Actions\Action::make('sendForSignature')
                        ->label(__("Send for signature"))
                        ->color("warning")
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation()
                        ->modalHeading(__("Confirmation"))
                        ->modalDescription(__("Are you sure you want to send this contract for signature?"))
                        ->action(function ($record, $livewire) {
                            return $livewire->redirect(route('docusign.contract', $record));
                        })
                        ->visible(function ($record) {
                            $status = data_get($record?->integration_data, 'status');
                            return !empty($record?->contractable?->customer) && (!$status || $status !== "completed");
                        }),
                    Tables\Actions\Action::make('downloadSignedContract')
                        ->label(__("Download generated contract"))
                        ->color("info")
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($record, $livewire) {
                            return $livewire->redirect(route('docusign.print_contract', $record));
                        })
                        ->visible(fn($record) => !empty(data_get($record?->integration_data, 'envelopeId'))),
                    Tables\Actions\EditAction::make()->after(fn($livewire) =>  $livewire->dispatch('refreshEventPage')),
                    Tables\Actions\DeleteAction::make()->after(fn($livewire) =>  $livewire->dispatch('refreshEventPage')),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }
}
