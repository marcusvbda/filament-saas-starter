<?php

namespace App\Http\Controllers;

use App\Models\Contract;

class EventsController extends Controller
{
    public function generateUrlToFill(Contract $contract)
    {
        $event = $contract->contractable;
        $event->eventFillUrl()->delete();
        $event->eventFillUrl()->create([
            'key' => md5(uniqid())
        ]);
    }
}
