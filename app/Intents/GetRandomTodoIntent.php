<?php

namespace App\Intents;

use App\Todo;

class GetRandomTodoIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $todo = Guest::forEvent($this->currentEvent->id)->open()->inRandomOrder()->first();
        if (!is_null($todo)) {
            return 'Zufällige offene Aufgabe: ' . $todo->todo;
        } else {
            return 'Es gibt zur Zeit keine offenen Aufgaben.';
        }
    }
}