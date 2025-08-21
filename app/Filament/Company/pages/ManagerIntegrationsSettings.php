<?php

namespace App\Filament\Company\Pages;

use App\Settings\IntegrationsSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerIntegrationsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 99;

    protected static string $settings = IntegrationsSettings::class;

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationGroup(): string
    {
        return __("Settings");
    }

    public static function getNavigationLabel(): string
    {
        return __("Integrations");
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('settings')->tabs([
                    Forms\Components\Tabs\Tab::make("DocuSign")->schema([
                        Forms\Components\TextInput::make('docusign.oauth_uri')
                            ->label('Url OAuth'),
                        Forms\Components\TextInput::make('docusign.base_uri')
                            ->label('Account Base URI'),
                        Forms\Components\TextInput::make('docusign.integration_key')
                            ->label('Integration Key'),
                        Forms\Components\TextInput::make('docusign.events_webhook_url')
                            ->label(__("Events Webhook URL"))
                            ->default(route('docusign.webhook')),
                        Forms\Components\TextInput::make('docusign.redirect_link')
                            ->label(__("Redirect link"))
                            ->hint(__("Keep it empty if you want to not redirect the user after signing"))
                            ->default("#"),
                    ]),
                ])
            ])->columns(1);
    }
}
