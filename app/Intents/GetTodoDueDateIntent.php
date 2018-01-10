<?php

namespace App\Intents;

use App\Todo;
use Carbon\Carbon;

class GetTodoDueDateIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;

        $todo = Todo::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->first();
        if (!empty($todo)) {
            if (is_null($todo->due_at)) {
                return 'Für ' . $todoName . ' ist kein Fälligkeitsdatum eingetragen.';
            } else {
                return $todoName . ' ist am ' . $todo->due_at->formatLocalized('%A den %d. %B %Y') . ' fällig.';
            }
        } else {
            return 'Ich kann keine Aufgabe namens ' . $todoName . ' für diese Veranstaltung finden.';
        }
    }
}