<?php

namespace App\Intents;

use App\Event;

class AddEventIntent extends Intent
{
    /* Required slot values */

    protected $requiredSlots = ['Veranstaltung', ];

    /* Methods */

    public function process()
    {
        $eventName = $this->slots['Veranstaltung']->value;

        /* Determine whether this event already exists */

        $event = Event::forUser($this->user->id)->where('name', 'LIKE', $eventName)->first();
        if (empty($event)) {
            $currentEvent = Event::create(['user_id' => $this->user->id, 'name' => $eventName, ]);
            $this->user->event_id = $currentEvent->id;
            $this->user->save();

            return 'Ich habe ' . $eventName . ' angelegt und zur aktiven Veranstaltung gemacht.';
        } else {
            $currentEvent = $event;
            $this->user->event_id = $currentEvent->id;
            $this->user->save();

            return 'Es gibt bereits eine Veranstaltung ' . $eventName . ' - ich habe diese zur aktiven Veranstaltung gemacht.';
        }
    }
}