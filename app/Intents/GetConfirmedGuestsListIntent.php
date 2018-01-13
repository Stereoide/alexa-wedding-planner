<?php

namespace App\Intents;

use App\Guest;

class GetConfirmedGuestsListIntent extends Intent
{
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
                $responseText = 'Folgende Gäste haben bereits zugesagt: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName . '.';
            }

            /* Determine adult and child guest numbers */

            $adultGuests = $guests->where('child_or_adult', 'adult');
            $childGuests = $guests->where('child_or_adult', 'child');

            if (!$adultGuests->isEmpty() || !$childGuests->isEmpty()) {
                $responseText .= ' Davon ';

                /* Adult guests */

                if ($adultGuests->isEmpty()) {
                    $responseText .= 'sind keine Gäste als Erwachsene';
                } else if ($adultGuests->count() == 1) {
                    $responseText .= 'ist ein Gast als Erwachsener';
                } else {
                    $responseText .= 'sind ' . $adultGuests->count() . ' Gäste als Erwachsene';
                }

                $responseText .= ' und ';

                /* Child guests */

                if ($childGuests->isEmpty()) {
                    $responseText .= 'keine Gäste';
                } else if ($childGuests->count() == 1) {
                    $responseText .= 'ein Gast';
                } else {
                    $responseText .= $childGuests->count() . ' Gäste';
                }

                $responseText .= ' als Kind eingetragen.';
            }

            return $responseText;
        }
    }
}