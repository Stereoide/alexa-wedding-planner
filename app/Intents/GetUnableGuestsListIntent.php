<?php

namespace App\Intents;

use App\Guest;

class GetUnableGuestsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch called off guests */

        $guests = Guest::forEvent($this->currentEvent->id)->unable()->get();

        if ($guests->isEmpty()) {
            return 'Es haben noch keine Gäste abgesagt.';
        } else {
            $firstGuestNames = $guests->pluck('name')->sort();
            $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

            if ($firstGuestNames->isEmpty()) {
                $responseText = 'Bisher hat nur ' . $lastGuestName . ' abgesagt.';
            } else {
                $responseText = 'Folgende Gäste haben abgesagt: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName;
            }

            return $responseText;
        }
    }
}