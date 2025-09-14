<?php

namespace App\Filament\Company\Widgets;

use App\Filament\Company\Resources\EventResource;
use App\Filament\Company\Resources\EventResource\Pages\CreateEvent;
use App\Models\Event;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $companyId = auth()->user()->current_company_id;

        return Event::query()
            ->where('start_date', '>=', $fetchInfo['start'])
            ->where('end_date', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn(Event $event) => [
                    'title' => $event->name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'url' => EventResource::getUrl(name: 'edit', parameters: ['tenant' => $companyId, 'record' => $event]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}
