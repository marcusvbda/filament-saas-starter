<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\ContractTemplateResource\Pages;
use App\Models\ContractTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Str;

class ContractTemplateResource extends Resource
{
    protected static ?string $model = ContractTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                ->helperText(
                    fn(Get $get) =>
                    __("Use the following tags") . " : {{customers.name}}, {{customers.email}}, {{customers.phone}}, {{customers.document}}, {{event.start_date}}, {{event.end_date}}, {{contract.name}}, {{contract.witnesses.0.name}}, {{contract.witnesses.n.email}} e {{additional_data.x}}"
                )
                ->reactive()
                ->required(),
            Forms\Components\Group::make()
                ->label(__('Additional fields'))
                ->schema([
                    Forms\Components\Repeater::make('additionalFields')
                        ->label(__('Additional fields'))
                        ->relationship('additionalFields')
                        ->schema([
                            Forms\Components\TextInput::make('data.key')
                                ->label(__('Key'))
                                ->required()
                                ->rules([
                                    'required',
                                    'distinct',
                                    'regex:/^[a-z0-9_]+$/'
                                ])
                                ->lazy()
                                ->afterStateUpdated(
                                    fn($state, callable $set) =>
                                    $set('data.key', Str::slug($state, '_'))
                                ),
                            Forms\Components\TextInput::make('data.label')
                                ->label(__('Label'))
                                ->required(),
                            Forms\Components\Select::make('data.type')
                                ->label(__('Type'))
                                ->options([
                                    'select'   => __('Select'),
                                    'text'     => __('Text'),
                                    'checkbox' => __('Checkbox'),
                                ])
                                ->required()
                                ->reactive(),
                            Forms\Components\Select::make('data.input_type')
                                ->label(__('Input type'))
                                ->options([
                                    'text'   => __('Text'),
                                    'number' => __('Number'),
                                    'email'  => __('Email'),
                                    'mask'   => __('Mask'),
                                ])
                                ->required()
                                ->visible(fn(Get $get) => $get('data.type') === 'text')
                                ->reactive(),
                            Forms\Components\TextInput::make('data.mask')
                                ->label(__('Mask'))
                                ->required()
                                ->visible(fn(Get $get) => $get('data.input_type') === 'mask' && $get('data.type') === 'text')
                                ->reactive(),
                            Forms\Components\Toggle::make('data.multiple')
                                ->label(__('Multiple'))
                                ->visible(fn(Get $get) => $get('data.type') === 'select'),
                            Forms\Components\TagsInput::make('data.options')
                                ->label(__('Options'))
                                ->required()
                                ->visible(fn(Get $get) => $get('data.type') === 'select'),
                            Forms\Components\Toggle::make('data.required')
                                ->label(__('Required')),
                            Forms\Components\TextInput::make('data.hint')
                                ->label(__('Hint')),
                        ])
                        ->columns(3)
                        ->orderColumn('sort_order')
                        ->defaultItems(0)
                ])
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn($record) => $record->trashed())
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
