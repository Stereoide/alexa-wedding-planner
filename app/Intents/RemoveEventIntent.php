<?php

namespace App\Intents;

use App\Event;

class RemoveEventIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Veranstaltung', ];

    /* Methods */

    public function process()
    {
        $eventName = $this->slots['Veranstaltung']->value;

        if ($eventName == 'Standardveranstaltung') {
            return 'Die Standardveranstaltung kann leider nicht gelöscht werden.';
        } else {
            /* Determine whether this event exists */

            $event = Event::forUser($this->user->id)->where('name', 'LIKE', $eventName)->first();
            if (!empty($event)) {
                /* Remove guests first */

                $event->guests()->delete();

                /* Remove event */

                $event->delete();

                /* Fetch default event */

                $currentEvent = Event::forUser($this->user->id)->where('name', 'LIKE', 'Standardveranstaltung')->first();
                $this->user->event_id = $currentEvent->id;
                $this->user->save();

                return 'Ich habe ' . $eventName . ' gelöscht. Ab sofort ist die Standardveranstaltung aktiv.';
            } else {
                return 'Ich konnte keine Veranstaltung ' . $eventName . ' finden.';
            }
        }
    }
}