<?php

namespace App\Intents;

use App\Event;

class GetTodaysEventsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->today()->get();

        if ($events->isEmpty()) {
            $responseText = 'Für heute sind keine Veranstaltungen geplant.';
        } else {
            if ($events->count() == 1) {
                $responseText = 'Für heute ist nur eine Veranstaltung geplant: ' . $events->first()->name;
            } else {
                $firstEventNames = $events->pluck('name')->sort();
                $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

                $responseText = 'Für heute sind die folgenden ' . $events->count() . ' Veranstaltungen geplant: ' . implode(', ', $firstEventNames->all()) . ' und ' . $lastEventName;
            }
        }

        return $responseText;
    }
}