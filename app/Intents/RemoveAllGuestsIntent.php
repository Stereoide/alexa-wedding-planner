<?php

namespace App\Intents;

use App\Guest;

class RemoveAllGuestsIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['', ];

    /* Methods */

    public function process()
    {
        Guest::forEvent($this->currentEvent->id)->delete();

        return 'Ich habe alle Gäste von der Gästeliste entfernt.';
    }
}