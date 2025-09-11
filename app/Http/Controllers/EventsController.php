<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Event;
use App\Models\EventFillUrl;

class EventsController extends Controller
{
    public function generateUrlToFill(Contract $contract)
    {
        $event = $contract->contractable;
        $event->eventFillUrl()->delete();
        $event->eventFillUrl()->create([
            'key' => md5(uniqid())
        ]);
        dd($event->eventFillUrl);
    }

    public function fillEventData($urlKey)
    {
        $url = EventFillUrl::where("key", $urlKey)->where("filled", "!=", true)->firstOrFail();
        $event = $url->event()->firstOrFail();
        dd($url, $event);
    }
}
