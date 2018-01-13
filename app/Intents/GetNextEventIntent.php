<?php

namespace App\Intents;

use App\Event;
use Carbon\Carbon;

class GetNextEventIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch next events */

        $todayEvents = Event::forUser($this->user->id)->today()->get();
        $futureEvents = Event::forUser($this->user->id)->future()->get();

        if (!$todayEvents->isEmpty()) {
            $responseText = '';

            $firstEventNames = $todayEvents->pluck('name')->sort();
            $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

            if ($firstEventNames->isEmpty()) {
                return 'Die Veranstaltung ' . $lastEventName . ' ist für heute geplant.';
            } else {
                return 'Folgende Veranstaltungen sind für heute geplant: ' . implode(', ', $firstEventNames->all()) . ' und ' . $lastEventName . '.';
            }
        } else if (!$futureEvents->isEmpty()) {
            $nextEvent = $futureEvents->first();
            return 'Als nächste Veranstaltung ist ' . $nextEvent->name . ' für ' . $nextEvent->event_at->formatLocalized('%A den %d. %B %Y') . ' geplant.';
        } else {
            return 'Aktuell sind für keine Veranstaltungen Termine in der Zukunft eingetragen.';
        }
    }
}