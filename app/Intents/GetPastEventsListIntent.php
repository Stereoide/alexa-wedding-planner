<?php

namespace App\Intents;

use App\Event;

class GetPastEventsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->inPast()->get()->sortBy('event_at');

        if ($events->isEmpty()) {
            $responseText = 'Es haben keine Veranstaltungen in der Vergangenheit stattgefunden.';
        } else if ($events->count() == 1) {
            $event = $events->first();
            $responseText = 'Es hat nur eine Veranstaltung in der Vergangenheit stattgefunden: ' . $event->name . ' am ' . $event->event_at->formatLocalized('%d.%m.%Y') . '.';
        } else {
            $firstEvents = $events->sortBy('event_at');
            $lastEvent = $firstEvents->splice($firstEvents->count() - 1)->first();

            $responseText = 'Die folgenden ' . $events->count() . ' Veranstaltungen haben in der Vergangenheit stattgefunden: ';
            $firstEvents->each(function($event) use (&$responseText) {
                $responseText .= $event->name . ' am ' . $event->event_at->formatLocalized('%d.%m.%Y') . ', ';
            });

            $responseText = substr($responseText, 0, strlen($responseText) - 2) . ' und ';
            $responseText .= $lastEvent->name . ' am ' . $lastEvent->event_at->formatLocalized('%d.%m.%Y') . ', ';
        }

        return $responseText;
    }
}