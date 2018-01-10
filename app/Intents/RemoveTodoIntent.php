<?php

namespace App\Intents;

use App\Todo;

class RemoveTodoIntent extends Intent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;

        Todo::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->delete();
        return 'Ich habe ' . $todoName . ' von der Todo-Liste entfernt.';
    }
}