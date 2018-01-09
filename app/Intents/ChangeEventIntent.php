<?php

namespace App\Intents;

use App\Event;

class ChangeEventIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Veranstaltung', ];

    /* Methods */

    public function process()
    {
        $eventName = $this->slots['Veranstaltung']->value;

        /* Determine whether this event exists */

        $event = Event::forUser($this->user->id)->where('name', 'LIKE', $eventName)->first();
        if (!empty($event)) {
            $currentEvent = $event;
            $this->user->event_id = $currentEvent->id;
            $this->user->save();

            return 'Ich habe ' . $eventName . ' zur aktiven Veranstaltung gemacht.';
        } else {
            return 'Ich konnte keine Veranstaltung ' . $eventName . ' finden.';
        }
    }
}