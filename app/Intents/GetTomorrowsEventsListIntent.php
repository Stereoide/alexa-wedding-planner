<?php

namespace App\Intents;

use App\Event;

class GetTomorrowsEventsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->tomorrow()->get();

        if ($events->isEmpty()) {
            $responseText = 'Für morgen sind keine Veranstaltungen geplant.';
        } else {
            if ($events->count() == 1) {
                $responseText = 'Für morgen ist nur eine Veranstaltung geplant: ' . $events->first()->name;
            } else {
                $firstEventNames = $events->pluck('name')->sort();
                $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

                $responseText = 'Für morgen sind die folgenden ' . $events->count() . ' Veranstaltungen geplant: ' . implode(', ', $firstEventNames->all()) . ' und ' . $lastEventName;
            }
        }

        return $responseText;
    }
}