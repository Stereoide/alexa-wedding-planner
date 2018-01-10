<?php

namespace App\Intents;

use App\Todo;

class GetCompletedTodosListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        $todos = Todo::forEvent($this->currentEvent->id)->completed()->get();

        if ($todos->isEmpty()) {
            return 'Es wurden noch keine Aufgaben erledigt.';
        } else {
            $firstTodoNames = $todos->pluck('todo')->sort();
            $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();

            if ($firstTodoNames->isEmpty()) {
                $responseText = 'Bisher wurde nur ' . $lastTodoName . ' erledigt.';
            } else {
                $responseText = 'Folgende Aufgaben wurden bereits erledigt: ' . implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName;
            }

            return $responseText;
        }
    }
}