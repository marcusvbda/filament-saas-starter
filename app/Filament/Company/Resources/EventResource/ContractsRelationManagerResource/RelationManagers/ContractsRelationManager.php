<?php

namespace App\Filament\Company\Resources\EventResource\ContractsRelationManagerResource\RelationManagers;

use App\Filament\Company\Resources\ContractTemplateResource;
use App\Models\ContractTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    public static function getLabel(): string
    {
        return __('Contracts');
    }

    public static function getPluralLabel(): string
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
            ->schema([
                Forms\Components\Section::make(__('Main informations'))->schema($this->getFormSchema())
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('contractTemplate.name'),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/Y - H:i'),
                Tables\Columns\TextColumn::make('updated_at')->date('d/m/Y - H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
