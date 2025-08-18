<?php

namespace App\Filament\Pages;

use App\Settings\IntegrationsSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerIntegrationsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 99;

    protected static string $settings = IntegrationsSettings::class;

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return "Integrações";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('settings')->tabs([
                    Forms\Components\Tabs\Tab::make("Email")->schema([
                        Forms\Components\TextInput::make('email.host')
                            ->label('Host'),
                        Forms\Components\TextInput::make('email.port')
                            ->label('Porta'),
                        Forms\Components\TextInput::make('email.user_name')
                            ->label('Usuário'),
                        Forms\Components\TextInput::make('email.password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                    ]),
                ])
            ])->columns(1);
    }
}
