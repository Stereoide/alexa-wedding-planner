<?php

namespace App\Intents;

use App\Guest;

class RemoveGuestIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Name', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Name']->value;

        Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->delete();
        return 'Ich habe ' . $guestName . ' von der Gästeliste entfernt.';
    }
}