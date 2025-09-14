<?php

namespace App\Filament\Company\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as FilamentRegister;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Wallo\FilamentCompanies\FilamentCompanies;
use Wallo\FilamentCompanies\Pages\Auth\PrivacyPolicy;
use Wallo\FilamentCompanies\Pages\Auth\Terms;

class RegisterCustomPage extends FilamentRegister
{
    protected static string $view = 'filament-companies::auth.register';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getBetaKeyValidationComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                ...FilamentCompanies::hasTermsAndPrivacyPolicyFeature() ? [$this->getTermsFormComponent()] : []
            ])
            ->statePath('data')
            ->model(FilamentCompanies::userModel());
    }

    protected function getBetaKeyValidationComponent(): Component
    {
        return TextInput::make('beta_register_key')
            ->label(__("Beta key"))
            ->helperText(__("If you don't have a beta key to register yet, ask to the adminstrator. It'll be required while this app is on beta tests"))
            ->password()
            ->required()
            ->rule(function () {
                return function (string $attribute, $value, $fail) {
                    if ($value !== config('app.beta_register_key')) {
                        $fail(__("Invalid beta key"));
                    }
                };
            });
    }

    protected function getTermsFormComponent(): Component
    {
        return Checkbox::make('terms')
            ->label(new HtmlString(__('filament-companies::default.subheadings.auth.register', [
                'terms_of_service' => $this->generateFilamentLink(Terms::getRouteName(), __('filament-companies::default.links.terms_of_service')),
                'privacy_policy' => $this->generateFilamentLink(PrivacyPolicy::getRouteName(), __('filament-companies::default.links.privacy_policy')),
            ])))
            ->validationAttribute(__('filament-companies::default.errors.terms'))
            ->accepted();
    }

    public function generateFilamentLink(string $routeName, string $label): string
    {
        return Blade::render('filament::components.link', [
            'href' => FilamentCompanies::route($routeName),
            'target' => '_blank',
            'color' => 'primary',
            'slot' => $label,
        ]);
    }
}
