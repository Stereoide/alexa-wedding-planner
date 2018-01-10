<?php

namespace App\Intents;

use App\Todo;

class GetOpenTodosListIntent extends Intent
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
                $responseText = 'Folgende Aufgaben wurden noch nicht erledigt: ' . implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . '.';
            }

            /* Check for overdue todos */

            $dueTodosResponseText = '';

            $overdueOpenTodos = Todo::forEvent($this->currentEvent->id)->open()->overdue()->get();
            if ($overdueOpenTodos->count() == 1) {
                $dueTodosResponseText .= $overdueOpenTodos->first()->todo . ' ist bereits überfällig';
            } else if ($overdueOpenTodos->count() > 1) {
                $firstTodoNames = $overdueOpenTodos->pluck('todo')->sort();
                $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();
                $dueTodosResponseText .= implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . ' sind bereits überfällig';
            }

            /* Check for todos due today */

            $dueTodosResponseText .= (!empty($dueTodosResponseText) ? ', ' : '');

            $dueTodayTodos = Todo::forEvent($this->currentEvent->id)->open()->dueToday()->get();
            if ($dueTodayTodos->count() == 1) {
                $dueTodosResponseText .= $dueTodayTodos->first()->todo . ' ist heute fällig';
            } else if ($dueTodayTodos->count() > 1) {
                $firstTodoNames = $dueTodayTodos->pluck('todo')->sort();
                $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();
                $dueTodosResponseText .= implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . ' sind heute fällig';
            }

            /* Check for todos due in the next five days */

            $dueTodosResponseText .= (!empty($dueTodosResponseText) ? ' und ' : '');
            $dueDays = 5;

            $dueNextDaysTodos = Todo::forEvent($this->currentEvent->id)->open()->dueInTheNextFewDays()->get();
            if ($dueNextDaysTodos->count() == 1) {
                $dueTodosResponseText .= $dueNextDaysTodos->first()->todo . ' ist in den nächsten ' . $dueDays . ' Tagen fällig.';
            } else if ($dueNextDaysTodos->count() > 1) {
                $firstTodoNames = $dueNextDaysTodos->pluck('todo')->sort();
                $lastTodoName = $firstTodoNames->splice($firstTodoNames->count() - 1)->first();
                $dueTodosResponseText .= implode(', ', $firstTodoNames->all()) . ' und ' . $lastTodoName . ' sind in den nächsten ' . $dueDays . ' Tagen fällig';
            }

            $responseText = trim($responseText . ' ' . $dueTodosResponseText);

            return $responseText;
        }
    }
}