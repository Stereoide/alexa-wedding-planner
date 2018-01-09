<?php

namespace App\Intents;

use App\Event;

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