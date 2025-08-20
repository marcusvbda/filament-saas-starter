<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.user-details';

    public static function getNavigationGroup(): string
    {
        return __('Registers');
    }

    public static function getLabel(): string
    {
        return __('Customer');
    }

    public static function getPluralLabel(): string
    {
        return __('Customers');
    }

    public static function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
            Forms\Components\TextInput::make('document')->label(__("Document"))->required(),
            Forms\Components\TextInput::make('email')->email()->required()->label('E-mail'),
            Forms\Components\TextInput::make('phone')->label(__("Phone")),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main informations'))->schema(static::getFormSchema())
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__("Name"))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__("Email"))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('document')->label(__("Document"))->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
