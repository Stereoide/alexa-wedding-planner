<?php

namespace App\Intents;

use App\Todo;

class AddTodoIntent
{
    /* Required slot values */

    public $requiredSlots = ['Todo', ];

    /* Methods */

    public function process()
    {
        $todoName = $this->slots['Todo']->value;

        /* Determine whether this guest already exists */

        $todos = Todo::forEvent($this->currentEvent->id)->where('todo', 'LIKE', $todoName)->get();
        if ($todos->isEmpty()) {
            Todo::create(['event_id' => $this->currentEvent->id, 'todo' => $todoName, 'status' => 'open']);
            return 'Ich habe ' . $todoName . ' zur Todo-Liste hinzugefügt';
        } else {
            return $todoName . ' stand bereits auf der Todoliste.';
        }
    }
}