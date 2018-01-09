<?php

namespace App\Intents;

use App\Event;

class GetConfirmedGuestsListIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = [];

    /* Methods */

    public function process()
    {
        $guests = Guest::forEvent($this->currentEvent->id)->confirmed()->get();

        if ($guests->isEmpty()) {
            return 'Es haben noch keine Gäste zugesagt.';
        } else {
            $firstGuestNames = $guests->pluck('name')->sort();
            $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

            if ($firstGuestNames->isEmpty()) {
                $responseText = 'Bisher hat nur ' . $lastGuestName . ' zugesagt.';
            } else {
                $responseText = 'Folgende Gäste haben bereits zugesagt: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName;
            }

            return $responseText;
        }
    }
}