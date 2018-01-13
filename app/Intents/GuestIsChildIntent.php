<?php

namespace App\Intents;

use App\Guest;

class GuestIsChildIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Guest', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Guest']->value;

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!is_null($guest)) {
            $previousStatus = $guest->child_or_adult;

            $guest->child_or_adult = 'child';
            $guest->save();

            switch ($previousStatus) {
                case 'adult' :
                    $responseText = $guestName . ' war bereits als Erwachsener eingetragen. Ich habe dies wie gewünscht geändert.';
                    break;

                case 'child' :
                    $responseText = $guestName . ' war bereits als Kind eingetragen.';
                    break;

                default :
                    $responseText = 'Ich habe ' . $guestName . ' als Kind eingetragen.';
                    break;
            }

            return $responseText;
        } else {
            return 'Ich konnte keinen Gast mit diesem Namen finden.';
        }
    }
}