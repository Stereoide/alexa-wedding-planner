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
        $guestName = $this->slots['Name']->value;

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!is_null($guest)) {
            switch ($guest->child_or_adult) {
                case 'adult' :
                    $responseText = $guestName . ' ist als Erwachsener eingetragen.';
                    break;

                case 'child' :
                    $responseText = $guestName . ' ist als Kind eingetragen.';
                    break;

                default :
                    $responseText = $guestName . ' ist bisher weder als als Kind noch als Erwachsener eingetragen.';
                    break;
            }

            return $responseText;
        } else {
            return 'Ich konnte keinen Gast mit diesem Namen finden.';
        }
    }
}