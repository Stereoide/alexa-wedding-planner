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

        $todo = Todo::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->first();
        if (!empty($todo)) {
            $todo->due_at = null;
            $todo->save();

            return 'Ich habe das Fälligkeitsdatum für ' . $todoName . ' gelöscht.';
        } else {
            return 'Ich kann keine Aufgabe namens ' . $todoName . ' für diese Veranstaltung finden.';
        }
    }
}