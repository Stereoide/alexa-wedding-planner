<?php

namespace App\Intents;

use App\Event;

class GetEventsWithoutDateListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->noDate()->get();

        if ($events->isEmpty()) {
            $responseText = 'Es sind für alle Veranstaltungen Termine eingetragen.';
        } else if ($events->count() == 1) {
            $event = $events->first();
            $responseText = 'Es gibt nur eine Veranstaltung ohne Termin: ' . $event->name . '.';
        } else {
            $firstEventNames = $events->pluck('name')->sort('name');
            $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

            $responseText = 'Die folgenden ' . $events->count() . ' Veranstaltungen haben noch keinen Termin hinterlegt: ';
            $responseText .= implode(', ', $firstEventNames) . ' und ' . $lastEventName . '.';
        }

        return $responseText;
    }
}