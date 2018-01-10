<?php

namespace App\Intents;

use App\Todo;

class GetRandomTodoIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $todo = Todo::forEvent($this->currentEvent->id)->open()->inRandomOrder()->first();
        if (!is_null($todo)) {
            $responseText = 'Zufällige offene Aufgabe: ' . $todo->todo;

            if (!is_null($todo->due_at)) {
                $responseText .= ', fällig am ' . $todo->due_at->formatLocalized('%A den %d. %B %Y');
            }

            return $responseText;
        } else {
            return 'Es gibt zur Zeit keine offenen Aufgaben.';
        }
    }
}