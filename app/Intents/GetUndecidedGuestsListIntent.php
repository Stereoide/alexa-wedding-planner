<?php

namespace App\Intents;

use App\Guest;

class GetUndecidedGuestsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch undecided guests */

        $guests = Guest::forEvent($this->currentEvent->id)->undecided()->get();

        if ($guests->isEmpty()) {
            return 'Es sind keine Anmeldungen mehr offen.';
        } else {
            $firstGuestNames = $guests->pluck('name')->sort();
            $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

            if ($firstGuestNames->isEmpty()) {
                $responseText = 'Bisher hat sich nur ' . $lastGuestName . ' noch nicht entschieden.';
            } else {
                $responseText = 'Folgende Gäste haben sich noch nicht entschieden: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName;
            }

            return $responseText;
        }
    }
}