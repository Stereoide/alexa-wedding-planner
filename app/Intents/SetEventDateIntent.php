<?php

namespace App\Intents;

use App\Event;
use Carbon\Carbon;

class SetEventDateIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Date', ];

    /* Methods */

    public function process()
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->slots['Date']->value);

        $currentEventDate = $this->currentEvent->event_at;

        $this->currentEvent->event_at = $date;
        $this->currentEvent->save();

        /* Prepare response */

        if (is_null($currentEventDate)) {
            return 'Ich habe das Veranstaltungsdatum für die aktuelle Veranstaltung auf ' . $date->formatLocalized('%A den %d. %B %Y') . ' gesetzt.';
        } else if ($currentEventDate == $date) {
            return 'Das Veranstaltungsdatum der aktuellen Veranstaltung war bereits auf ' . $date->formatLocalized('%A den %d. %B %Y') . ' gesetzt.';
        } else {
            return 'Das Veranstaltungsdatum der aktuellen Veranstaltung war auf ' . $currentEventDate->formatLocalized('%A den %d. %B %Y') . ' gesetzt - ich habe es wie gewünscht auf ' . $date->formatLocalized('%A den %d. %B %Y') . ' geändert.';
        }
    }
}