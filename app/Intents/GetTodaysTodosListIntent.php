<?php

namespace App\Intents;

use App\Todo;

class GetTodaysTodosListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all todos for today */

        $openTodos = Todo::forEvent($this->currentEvent->id)->open()->dueToday()->get();

        if ($openTodos->isEmpty()) {
            $responseText = 'Für heute sind keine Aufgaben geplant.';
        } else {
            if ($openTodos->count() == 1) {
                $responseText = 'Für heute ist nur ' . $openTodos->first()->todo . ' geplant.';
            } else {
                $firstTodoNames = $openTodos->pluck('todo')->sort();
                $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();
                $responseText = 'Für heute sind ' . $openTodos->count() . ' Aufgaben geplant: ' . implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . '.';
            }
       }

        return $responseText;
    }
}