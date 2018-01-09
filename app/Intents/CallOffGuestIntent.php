<?php

namespace App\Intents;

use App\Guest;

class CallOffGuestIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Name', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Name']->value;

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!is_null($guest)) {
            $guest->status = 'unable';
            $guest->save();
            return 'Ich habe die Absage für ' . $guestName . ' notiert.';
        } else {
            return 'Ich konnte keinen Gast mit diesem Namen finden.';
        }
    }
}