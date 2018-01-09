<?php

namespace App\Intents;

use App\Todo;

class ReopenTodoIntent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;

        $todo = Guest::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->first();
        if (!is_null($todo)) {
            $todo->status = 'open';
            $todo->save();
            return 'Ich habe ' . $todoName . ' als noch nicht erledigt markiert.';
        } else {
            return 'Ich konnte keine Aufgabe mit diesem Namen finden.';
        }
    }
}