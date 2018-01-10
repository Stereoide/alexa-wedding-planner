<?php

namespace App\Intents;

use App\Todo;

class RemoveDueDateFromTodoIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;

        return 'Diese Funktion wird noch nicht unterstützt';

        /* Determine whether this guest is registered for the current event */

        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
        if (!empty($guest)) {
            /* Determine whether this note is already registered for this guest */

            $note = GuestNote::forGuest($guest->id)->where('note', 'LIKE', $noteName)->first();
            if (empty($note)) {
                $note = GuestNote::create(['guest_id' => $guest->id, 'note' => $noteName, ]);

                return 'Ich habe die Notiz ' . $noteName . ' für ' . $guestName . ' angelegt.';
            } else {
                return 'Für ' . $guestName . ' war bereits eine Notiz ' . $noteName . ' hinterlegt.';
            }
        } else {
            return $guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.';
        }
    }
}