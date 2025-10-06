<?php

namespace App\Filament\Company\Pages;

use App\Models\Event;
use App\Models\EventFillUrl;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PublicFillFormEvent extends Page implements HasForms
{
    use InteractsWithForms;
    public ?Event $event;
    public ?EventFillUrl $url;

    protected static ?string $navigationIcon = null;
    protected static string $view = 'filament.pages.public-fill-form-event';
    protected static string $layout = 'filament-panels::components.layout.base';
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
        $this->data["witnesses"] =  data_get($this->event->contract, "witnesses", []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema(
                function () {
                    return array_merge(collect(
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
                            case 'text_repeater':
                                return Forms\Components\Repeater::make($key)
                                    ->label($field->data['label'])
                                    ->hint(fn() => $field->data['required'] ? __("Required at fill form link") : '')
                                    ->helperText($field->data['hint'] ?? null)
                                    ->itemLabel(function (array $state, $component): ?string {
                                        static $position = 1;
                                        return "#" . $position++;
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('value')
                                            ->label($field->data['item_label'])
                                            ->required($field->data['required'] ?? false)
                                    ])
                                    ->minItems($field->data['required'] ? 1 : 0)
                                    ->maxItems($field->data['max_length'] ?? null)
                                    ->collapsed(false)
                                    ->defaultItems(1)
                                    ->reorderable(false)
                                    ->collapsible(false)
                                    ->grid(2)
                                    ->columnSpanFull();
                            default:
                                return Forms\Components\TextInput::make($key)
                                    ->label($field->data['label'])
                                    ->required(fn() => $field->data['required'])
                                    ->helperText($field->data['hint'] ?? null);
                        }
                    })->toArray(), [
                        Forms\Components\Repeater::make('witnesses')
                            ->label(__("Witnesses"))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__("Name"))
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsed(false),
                    ]);
                }
            );
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $witnesses = [];
        if (@$data["witnesses"]) {
            $witnesses = $data["witnesses"];
            unset($data["witnesses"]);
            $this->event->contract->witnesses = $witnesses;
            $this->event->contract->save();
        }
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
