<?php

namespace App\Intents;

use App\Event;
use Carbon\Carbon;

class RemoveEventDateIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $this->currentEvent->event_at = null;
        $this->currentEvent->save();

        return 'Ich habe das Veranstaltungsdatum für die Veranstaltung ' . $this->currentEvent->name . ' entfernt.';
    }
}