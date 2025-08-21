<?php

namespace App\Filament\Company\Resources\EventResource\ContractsRelationManagerResource\RelationManagers;

use App\Filament\Company\Resources\ContractTemplateResource;
use App\Models\ContractTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

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
                ->createOptionForm(ContractTemplateResource::getFormFields())
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->label(__("Name"))->searchable(),
                Tables\Columns\TextColumn::make('contractTemplate.name')->label(__("Template")),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/Y - H:i')->label(__("Created at"))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->date('d/m/Y - H:i')->label(__("Updated at"))->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('Create contract')),
            ])
            ->actions([
                Tables\Actions\Action::make('sendForSignature')
                    ->label(__("Send for signature"))
                    ->color("info")
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->modalHeading(__("Confirmation"))
                    ->modalDescription(__("Are you sure you want to send this contract for signature?"))
                    ->url(fn($record) => route('docusign.contract', $record)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
