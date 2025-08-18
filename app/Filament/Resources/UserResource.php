<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Leandrocfe\FilamentPtbrFormFields\Document;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Usuários';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários';

    public static function getModelLabel(): string
    {
        return 'Usuário';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Usuários';
    }

    public static function getFormFields(bool $isCreate = true, $role = null): array
    {
        $fields = [
            Forms\Components\TextInput::make('name')->label("Nome")->required(),
            Forms\Components\TextInput::make('email')->label("E-mail")->email()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('profession')->label("Profissão"),
            Forms\Components\Select::make('civil_status')->label("Estado Civil")->options(User::$civilStatuses),
            Forms\Components\TextInput::make('nacionality')->label("Nacionalidade"),
            Document::make('cpf_or_cnpj')->label("CPF/CNPJ")->dynamic(),
            Forms\Components\Select::make('roles')
                ->label('Role')
                ->relationship('roles', 'name')
                ->required()
                ->default($role !== null ? [Role::where('name', $role)->value('id')] : null)
                ->dehydrated(true)
                ->preload(),
            Forms\Components\TextInput::make('password')
                ->label("Senha")
                ->password()
                ->required($isCreate)
                ->nullable(!$isCreate)
                ->revealable(),
            Forms\Components\TextInput::make('password_confirmation')
                ->label("Confirma Senha")
                ->password()
                ->required($isCreate)
                ->nullable(!$isCreate)
                ->revealable()
                ->same('password'),
        ];

        return $fields;
    }

    public static function form(Form $form): Form
    {
        $isCreate = $form->getOperation() === 'create';

        return $form
            ->schema([
                Forms\Components\Section::make("Informações")->schema(static::getFormFields($isCreate))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->label('Tipo')->getStateUsing(function ($record) {
                    return ucfirst(str_replace('_', ' ', $record->roles->first()?->name ?? 'N/A'));
                }),
                Tables\Columns\TextColumn::make('summary_detais')
                    ->label('Resumo')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $civilStatus = $record->civil_status ? User::$civilStatuses[$record->civil_status] : 'N/A';
                        return collect([
                            "<strong>Email : </strong>" . $record->email,
                            "<strong>CPF/CNPJ : </strong>" . $record->cpf_or_cnpj,
                            "<strong>Nacionalidade : </strong>" . $record->nacionality,
                            "<strong>Estado Civil : </strong>" . $civilStatus
                        ])->implode('<br>');
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Tipo')
                    ->relationship('roles', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('civil_status')
                    ->label('Estado Civil')
                    ->options(User::$civilStatuses)
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Criado de'),
                        Forms\Components\DatePicker::make('created_to')->label('Criado ate'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_to'],
                                fn($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
            RelationManagers\PhonesRelationManager::class,
            RelationManagers\AddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
