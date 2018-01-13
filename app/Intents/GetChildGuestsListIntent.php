<?php

namespace App\Intents;

use App\Guest;

class GetChildGuestsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $guests = Guest::forEvent($this->currentEvent->id)->confirmed()->child()->get();

        if ($guests->isEmpty()) {
            return 'Es haben noch keine Kinder zugesagt.';
        } else {
            $firstGuestNames = $guests->pluck('name')->sort();
            $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

            if ($firstGuestNames->isEmpty()) {
                $responseText = 'Bisher hat nur ein Kind zugesagt: ' . $lastGuestName . '.';
            } else {
                $responseText = 'Folgende Kinder haben bereits zugesagt: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName . '.';
            }

            return $responseText;
        }
    }
}