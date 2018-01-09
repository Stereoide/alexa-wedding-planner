<?php

namespace App\Intents;

use App\Event;

class RemoveAllNotesFromGuestIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Gast', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Gast']->value;

        /* Determine whether this guest is registered for the current event */

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!empty($guest)) {
            /* Remove all notes for this guest */

            GuestNote::forGuest($guest->id)->delete();

            return 'Ich habe alle Notizen für ' . $guestName . ' entfernt.';
        } else {
            return $guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.';
        }
    }
}