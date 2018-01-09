<?php

namespace App\Intents;

use App\Event;

class AddGuestIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Name', ];

    /* Methods */

    public function process()
    {
        $guestName = $this->slots['Name']->value;

        /* Determine whether this guest already exists */

        $guests = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->get();
        if ($guests->isEmpty()) {
            Guest::create(['event_id' => $this->currentEvent->id, 'name' => $guestName, 'status' => 'undecided']);
            return 'Ich habe ' . $guestName . ' zur Gästeliste hinzugefügt';
        } else {
            return $guestName . ' war bereits angemeldet.';
        }
    }
}