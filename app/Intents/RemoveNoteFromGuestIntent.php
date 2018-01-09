<?php

namespace App\Intents;

use App\Guest;
use App\GuestNote;

class RemoveNoteFromGuestIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Gast', 'Notiz', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Gast']->value;
        $noteName = $this->slots['Notiz']->value;

        /* Determine whether this guest is registered for the current event */

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!empty($guest)) {
            /* Determine whether this note is registered for this guest */

            $note = GuestNote::forGuest($guest->id)->where('note', 'LIKE', $noteName)->first();
            if (!empty($note)) {
                $note->delete();

                return 'Ich habe die Notiz ' . $noteName . ' für ' . $guestName . ' entfernt.';
            } else {
                return 'Für ' . $guestName . ' war keine Notiz ' . $noteName . ' hinterlegt.';
            }
        } else {
            return $guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.';
        }
    }
}