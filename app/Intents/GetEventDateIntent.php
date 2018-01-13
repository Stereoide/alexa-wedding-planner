<?php

namespace App\Intents;

use App\Event;
use Carbon\Carbon;

class GetEventDateIntent extends Intent
{
    /* Methods */

    public function process()
    {
        if (is_null($this->currentEvent->event_at)) {
            return 'Für die aktuelle Veranstaltung ist noch kein Veranstaltungsdatum eingetragen.';
        } else {
            return 'Die aktuelle Veranstaltung ist für ' . $this->currentEvent->event_at->formatLocalized('%A den %d. %B %Y') . ' geplant.';
        }
    }
}