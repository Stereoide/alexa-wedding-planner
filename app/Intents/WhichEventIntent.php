<?php

namespace App\Intents;

use App\Event;

class WhichEventIntent extends Intent
{
    /* Methods */

    public function process()
    {
        return 'Aktuell ist ' . $this->currentEvent->name . ' die aktive Veranstaltung.';
    }
}