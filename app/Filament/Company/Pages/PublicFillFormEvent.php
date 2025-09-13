<?php

namespace App\Filament\Company\Pages;

use App\Models\Event;
use App\Models\EventFillUrl;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PublicFillFormEvent extends Page implements HasForms
{
    use InteractsWithForms;
    public ?Event $event;
    public ?EventFillUrl $url;

    protected static ?string $navigationIcon = null;
    protected static string $view = 'filament.pages.public-fill-form-event';
    protected static string $layout = 'filament-panels::components.layout.simple';
    protected static bool $shouldRegisterNavigation = false;
    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount($key)
    {
        $this->url = EventFillUrl::where("key", $key)->firstOrFail();
        $this->event = $this->url->event()->firstOrFail();
        $this->data = collect($this->event?->contract?->contractTemplate?->additionalFields()?->get() ?? [])
            ->mapWithKeys(function ($field) {
                $key = data_get($field->data, 'key');
                $value = data_get($this->event, "additional_data.$key", null);
                return [$key => $value];
            })->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema(
                function () {
                    return collect(
                        $this->event?->contract?->contractTemplate?->additionalFields()?->get() ?? []
                    )->map(function ($field) {
                        $key = data_get($field->data, 'key');
                        $type = data_get($field->data, 'type');
                        switch ($type) {
                            case 'text':
                                return Forms\Components\TextInput::make($key)
                                    ->default(data_get($this->event, "additional_data.$key"))
                                    ->label($field->data['label'])
                                    ->required(fn() => $field->data['required'])
                                    ->helperText($field->data['hint'] ?? null);
                            case 'select':
                                return Forms\Components\Select::make($key)
                                    ->label($field->data['label'])
                                    ->options(collect($field->data['options'] ?? [])->mapWithKeys(fn($o) => [$o => $o]))
                                    ->multiple($field->data['multiple'] ?? false)
                                    ->required(fn() => $field->data['required'])
                                    ->helperText($field->data['hint'] ?? null);
                            case 'checkbox':
                                return Forms\Components\Toggle::make($key)
                                    ->label($field->data['label'])
                                    ->required(fn() => $field->data['required'])
                                    ->helperText($field->data['hint'] ?? null);
                            default:
                                return Forms\Components\TextInput::make($key)
                                    ->label($field->data['label'])
                                    ->required(fn() => $field->data['required'])
                                    ->helperText($field->data['hint'] ?? null);
                        }
                    })->toArray();
                }
            );
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $this->event->additional_data = array_merge($this->event->additional_data ?? [], $data);
        $this->url->filled = true;
        $this->url->save();
        $this->event->save();

        Notification::make()
            ->title(__("Thank you, your data was sent succefully"))
            ->success()
            ->send();

        $this->dispatch("refresh");
    }
}
