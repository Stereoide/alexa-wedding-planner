<?php

namespace App\Intents;

use App\Todo;

class GetOverdueTodosListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all todos for today */

        $openTodos = Todo::forEvent($this->currentEvent->id)->open()->overdue()->get();

        if ($openTodos->isEmpty()) {
            $responseText = 'Es gibt keine überfälligen Aufgaben.';
        } else {
            $firstTodoNames = $openTodos->pluck('todo')->sort();
            $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();
            $responseText = 'Folgende Aufgaben sind bereits überfällig: ' . implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . '.';
       }

        return $responseText;
    }
}