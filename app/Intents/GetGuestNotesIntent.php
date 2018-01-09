<?php

namespace App\Intents;

use App\Guest;
use App\GuestNote;

class GetGuestNotesIntent extends Intent
{
    /* Optional slot values */

    public $optionalSlots = ['Gast', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Gast']->value ?? null;

        if (!empty($guestName)) {
            /* Determine whether this guest is registered for the current event */

            $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
            return 'gast-id: ' . $guest->id;

            if (!empty($guest)) {
                /* Fetch notes for this guest */

                $notes = GuestNote::forGuest($guest->id)->get();

                if ($notes->isEmpty()) {
                    return 'Für ' . $guestName . ' sind keine Notizen eingetragen.';
                } else if ($notes->count() == 1) {
                    return 'Für ' . $guestName . ' ist folgende Notiz eingetragen: ' . $notes->first()->pluck('note')->first();
                } else {
                    $firstNotes = $notes->pluck('note');
                    $lastNote = $firstNotes->splice($firstNotes->count() - 1)->first();

                    return 'Folgende Notizen sind für ' . $guestName . ' eingetragen: ' . implode(', ', $firstNotes->all()) . ' und ' . $lastNote;
                }
            } else {
                return $guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.';
            }
        } else {
            /* First fetch guests with notes */

            $guests = Guest::forEvent($this->currentEvent->id)->has('notes')->get();
            if (!$guests->isEmpty()) {
                $responseText = 'Für folgende Gäste sind Notizen hinterlegt. ';

                $guests->each(function($guest) use (&$responseText) {
                    $responseText .= $guest->name . ': ' . implode(', ', $guest->notes->pluck('note')->all()) . '. ';
                });

                return $responseText;
            } else {
                return 'Es ist für keinen Gast eine Notiz hinterlegt';
            }
        }
    }
}