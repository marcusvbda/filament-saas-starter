<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Leandrocfe\FilamentPtbrFormFields\Cep;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    public $loadingCities = false;
    public $cities = [];

    public static function getModelLabel(): string
    {
        return 'Endereço';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Endereços';
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        if ($ownerRecord instanceof Customer) {
            return static::getPluralModelLabel() . ' de ' . $ownerRecord->name;
        }

        return static::getPluralModelLabel();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Cep::make('zipcode')
                    ->label('CEP')
                    ->viaCep(
                        mode: 'suffix',
                        errorMessage: 'CEP inválido.',
                        setFields: [
                            'street' => 'logradouro',
                            'number' => 'numero',
                            'complement' => 'complemento',
                            'district' => 'bairro',
                            'city' => 'localidade',
                            'state' => 'uf'
                        ]
                    )->required(),
                Forms\Components\TextInput::make('street')
                    ->label('Logradouro')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('complement')
                    ->label('Complemento')
                    ->maxLength(255),
                Forms\Components\TextInput::make('district')
                    ->label('Bairro')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                    ->maxLength(2),
                Forms\Components\TextInput::make('city')
                    ->label('Cidade')
                    ->required()
                    ->maxLength(255),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('street')
                    ->label('Logradouro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Bairro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => strtoupper($state)),
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
