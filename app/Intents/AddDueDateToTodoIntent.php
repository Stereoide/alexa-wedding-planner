<?php

namespace App\Intents;

use App\Todo;
use Carbon\Carbon;

class AddDueDateToTodoIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', 'Date', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;
        $dueDate = Carbon::createFromFormat('Y-m-d', $this->slots['Date']->value);

        $todo = Todo::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->first();
        if (!empty($todo)) {
            $todo->due_at = $dueDate;
            $todo->save();

            return 'Ich habe das Fälligkeitsdatum für ' . $todoName . ' auf ' . $dueDate->formatLocalized('%A den %d. %B %Y') . ' gesetzt.';
        } else {
            return 'Ich kann keine Aufgabe namens ' . $todoName . ' für diese Veranstaltung finden.';
        }
    }
}