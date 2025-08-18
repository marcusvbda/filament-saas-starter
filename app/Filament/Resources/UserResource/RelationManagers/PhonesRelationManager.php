<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Leandrocfe\FilamentPtbrFormFields\PhoneNumber;

class PhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'phones';
    public static function getModelLabel(): string
    {
        return 'Telefone';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Telefones';
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
                Forms\Components\TextInput::make('type')
                    ->label('Tipo')
                    ->required()
                    ->maxLength(255),
                PhoneNumber::make('number')
                    ->label('Número de Telefone')
                    ->required()
                    ->maxLength(20)
                    ->tel(),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1)$2-$3', $state))
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
