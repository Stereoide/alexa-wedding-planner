<?php

namespace App\Intents;

use App\Event;

class GetEventsListIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = [];

    /* Methods */

    public function process()
    {
        /* Fetch all events */

        $events = Event::forUser($this->user->id)->get();

        if ($events->isEmpty()) {
            $responseText = 'Es liegen aktuell keine Veranstaltungen vor.';
        } else {
            if ($events->count() == 1) {
                $responseText = 'Es gibt zur Zeit nur eine Veranstaltung: ' . $events->first()->name;
            } else {
                $firstEventNames = $events->pluck('name')->sort();
                $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

                $responseText = 'Es gibt ' . $events->count() . ' Veranstaltungen: ' . implode(', ', $firstEventNames->all()) . ' und ' . $lastEventName;
            }
        }

        return $responseText;
    }
}