<?php

namespace App\Intents;

use App\Event;

class WhichEventIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = [];

    /* Methods */

    public function process()
    {
        return 'Aktuell ist ' . $this->currentEvent->name . ' die aktive Veranstaltung.';
    }
}