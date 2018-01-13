<?php

namespace App\Intents;

use App\Guest;

class GetAdultGuestsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $guests = Guest::forEvent($this->currentEvent->id)->confirmed()->adult()->get();

        if ($guests->isEmpty()) {
            return 'Es haben noch keine Erwachsenen zugesagt.';
        } else {
            $firstGuestNames = $guests->pluck('name')->sort();
            $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

            if ($firstGuestNames->isEmpty()) {
                $responseText = 'Bisher hat nur ein Erwachsener zugesagt: ' . $lastGuestName . '.';
            } else {
                $responseText = 'Folgende Erwachsenen haben bereits zugesagt: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName . '.';
            }

            return $responseText;
        }
    }
}