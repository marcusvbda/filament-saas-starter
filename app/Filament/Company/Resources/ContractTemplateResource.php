<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\ContractTemplateResource\Pages;
use App\Models\ContractTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ContractTemplateResource extends Resource
{
    protected static ?string $model = ContractTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static $templateTags = [
        '{{name}}',
        '{{email}}',
        '{{document}}',
    ];

    public static function getNavigationLabel(): string
    {
        return static::getPluralModelLabel();
    }

    public static function getNavigationGroup(): string
    {
        return __('Contracts');
    }

    public static function getModelLabel(): string
    {
        return __("Template Contract");
    }

    public static function getPluralModelLabel(): string
    {
        return __("Contract Templates");
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
            TinyEditor::make('content')
                ->label(__("Content"))
                ->hint(__("Use the following tags") . " : " . implodeSuffix(",", static::$templateTags))
                ->required(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main informations'))->schema(static::getFormFields())
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__("Name"))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label(__("Created at"))->sortable()->searchable()->date('d/m/Y - H:i'),
                Tables\Columns\TextColumn::make('updated_at')->label(__("Updated at"))->sortable()->searchable()->date('d/m/Y - H:i'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTemplates::route('/'),
            'create' => Pages\CreateContractTemplate::route('/create'),
            'edit' => Pages\EditContractTemplate::route('/{record}/edit'),
        ];
    }
}
