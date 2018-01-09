<?php

namespace App\Intents;

use App\Todo;

class GetOpenTodosListIntent
{
    /* Methods */

    public function process()
    {
        $todos = Todo::forEvent($this->currentEvent->id)->open()->get();

        if ($todos->isEmpty()) {
            return 'Es sind keine Aufgaben mehr zu erledigen.';
        } else {
            $firstTodoNames = $todos->pluck('todo')->sort();
            $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();

            if ($firstTodoNames->isEmpty()) {
                $responseText = 'Es ist nur noch ' . $lastTodoName . ' zu erledigen.';
            } else {
                $responseText = 'Folgende Aufgaben wurden noch nicht erledigt: ' . implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName;
            }

            return $responseText;
        }
    }
}