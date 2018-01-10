<?php

namespace App\Intents;

use App\Todo;

class GetNextTodoIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $todo = Guest::forEvent($this->currentEvent->id)->open()->first();
        if (!is_null($todo)) {
            return 'Nächste offene Aufgabe: ' . $todo->todo;
        } else {
            return 'Es gibt zur Zeit keine offenen Aufgaben.';
        }
    }
}