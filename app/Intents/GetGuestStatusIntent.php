<?php

namespace App\Intents;

use App\Event;

class GetGuestStatusIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Name', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Name']->value;

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!is_null($guest)) {
            switch ($guest->status) {
                case 'confirmed' :
                    return $guestName . ' hat zugesagt.';

                    break;

                case 'unable' :
                    return $guestName . ' hat abgesagt.';

                    break;

                case 'undecided' :
                    return $guestName . ' hat sich noch nicht entschieden.';

                    break;

                default :
                    return $guestName . ' hat einen unbekannten Anmeldestsatus';
            }
        } else {
            return 'Ich konnte keinen Gast mit diesem Namen finden.';
        }
    }
}