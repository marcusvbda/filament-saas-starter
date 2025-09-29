<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\EventResource\Pages;
use App\Filament\Company\Resources\EventResource\RelationManagers\ContractRelationManager;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): string
    {
        return __('Registers');
    }

    public static function getLabel(): string
    {
        return __('Event');
    }

    public static function getPluralLabel(): string
    {
        return __('Events');
    }

    protected static function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\DateTimePicker::make('start_date')->label(__("Start date"))->required(),
                Forms\Components\DateTimePicker::make('end_date')->label(__("End date"))->required(),
            ]),
            Forms\Components\Select::make("customer")
                ->label(__("Customer"))
                ->relationship('customer', 'name')
                ->searchable()
                ->createOptionForm(CustomerResource::getFormSchema())
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Main informations'))->schema(static::getFormSchema()),
                Forms\Components\Section::make(__('Additional fields'))
                    ->schema(
                        function (Get $get, $record) {
                            $fields = [];
                            if (!empty($record->eventFillUrl) && $record->eventFillUrl?->filled !== true) {
                                $url = route('event.fill_data', $record->eventFillUrl->key);

                                $fields[] = Forms\Components\Placeholder::make('fill_url_alert')->hiddenLabel()
                                    ->content(new HtmlString('<a href="' . e($url) . '" target="_blank" class="underline text-primary">' . e($url) . '</a>'));
                            }
                            $customFields = collect(
                                $record?->contract?->contractTemplate?->additionalFields()?->get() ?? []
                            )->map(function ($field) use ($record) {
                                $key = data_get($field->data, 'key');
                                $type = data_get($field->data, 'type');
                                switch ($type) {
                                    case 'text':
                                        return Forms\Components\TextInput::make("additional_data.{$key}")
                                            ->label($field->data['label'])
                                            ->hint(fn() => $field->data['required'] ? __("Required at fill form link") : '')
                                            ->helperText($field->data['hint'] ?? null);
                                    case 'select':
                                        return Forms\Components\Select::make("additional_data.{$key}")
                                            ->label($field->data['label'])
                                            ->options(collect($field->data['options'] ?? [])->mapWithKeys(fn($o) => [$o => $o]))
                                            ->multiple($field->data['multiple'] ?? false)
                                            ->hint(fn() => $field->data['required'] ? __("Required at fill form link") : '')
                                            ->helperText($field->data['hint'] ?? null);
                                    case 'checkbox':
                                        return Forms\Components\Toggle::make("additional_data.{$key}")
                                            ->label($field->data['label'])
                                            ->hint(fn() => $field->data['required'] ? __("Required at fill form link") : '')
                                            ->helperText($field->data['hint'] ?? null);
                                    default:
                                        return Forms\Components\TextInput::make("additional_data.{$key}")
                                            ->label($field->data['label'])
                                            ->hint(fn() => $field->data['required'] ? __("Required at fill form link") : '')
                                            ->helperText($field->data['hint'] ?? null);
                                }
                            })->toArray();

                            return array_merge($fields, $customFields);
                        }
                    )
                    ->visible(fn(Get $get, $record) =>  $record?->contract?->contractTemplate?->additionalFields?->count())
                    ->reactive(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__("Name"))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label(__("Customer"))->sortable(),
                Tables\Columns\TextColumn::make('start_date')->label(__("Start date"))->searchable()->sortable()->date('d/m/Y - H:i'),
                Tables\Columns\TextColumn::make('end_date')->label(__("End date"))->sortable()->searchable()->date('d/m/Y - H:i'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->label(__('From')),
                        Forms\Components\DatePicker::make('end_date')->label(__('Until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date'], fn($q) => $q->where('start_date', '>=', $data['start_date']))
                            ->when($data['end_date'], fn($q) => $q->where('end_date', '<=', $data['end_date']));
                    }),
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
            ContractRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
