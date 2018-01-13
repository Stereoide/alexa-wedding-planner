<?php

namespace App\Intents;

use App\Event;

class GetFutureEventsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->inFuture()->get()->sortBy('event_at');

        if ($events->isEmpty()) {
            $responseText = 'Aktuell sind keine Veranstaltungen für die Zukunft geplant.';
        } else if ($events->count() == 1) {
            $event = $events->first();
            $responseText = 'Aktuell ist nur eine Veranstaltung für die Zukunft geplant: ' . $event->name . ' am ' . $event->event_at->formatLocalized('%d.%m.%Y') . '.';
        } else {
            $firstEvents = $events->sortBy('event_at');
            $lastEvent = $firstEvents->splice($firstEvents->count() - 1)->first();

            $responseText = 'Die folgenden ' . $events->count() . ' Veranstaltungen sind für die Zukunft geplant: ';
            $firstEvents->each(function($event) use (&$responseText) {
                $responseText .= $event->name . ' am ' . $event->event_at->formatLocalized('%d.%m.%Y') . ', ';
            });

            $responseText = substr($responseText, 0, strlen($responseText) - 2) . ' und ';
            $responseText .= $lastEvent->name . ' am ' . $lastEvent->event_at->formatLocalized('%d.%m.%Y') . ', ';
        }

        return $responseText;
    }
}