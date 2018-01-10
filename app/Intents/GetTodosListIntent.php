<?php

namespace App\Intents;

use App\Todo;

class GetTodosListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all todos */

        $completedTodos = Todo::forEvent($this->currentEvent->id)->completed()->get();
        $openTodos = Todo::forEvent($this->currentEvent->id)->open()->get();

        if ($completedTodos->isEmpty() && $openTodos->isEmpty()) {
            $responseText = 'Es liegen noch keine Aufgaben vor.';
        } else {
            $responseText = '';

            if ($completedTodos->isEmpty()) {
                $responseText .= 'Es wurden noch keine Aufgaben erledigt';
            } else {
                if ($completedTodos->count() == 1) {
                    $responseText .= 'Eine Aufgabe wurde erledigt';
                } else {
                    $responseText .= $completedTodos->count() . ' Aufgaben wurden erledigt';
                }
            }

            $responseText .= ' und ';
            if ($openTodos->isEmpty()) {
                $responseText .= 'es sind keine Aufgaben mehr offen.';
            } else {
                if ($openTodos->count() == 1) {
                    $responseText .= 'eine Aufgabe ist noch offen.';
                } else {
                    $responseText .= $openTodos->count() . ' Aufgaben sind noch offen.';
                }

                /* Check for overdue todos */

                $dueTodosResponseText = '';

                $overdueOpenTodos = Todo::forEvent($this->currentEvent->id)->open()->overdue()->get();
                if ($overdueOpenTodos->count() == 1) {
                    $dueTodosResponseText .= 'Eine Aufgabe ist bereits überfällig';
                } else if ($overdueOpenTodos->count() > 1) {
                    $dueTodosResponseText .= $overdueOpenTodos->count() . ' Aufgaben sind bereits überfällig';
                }

                /* Check for todos due today */

                $dueTodosResponseText .= (!empty($dueTodosResponseText) ? ', ' : '');

                $dueTodayTodos = Todo::forEvent($this->currentEvent->id)->open()->dueToday()->get();
                if ($dueTodayTodos->count() == 1) {
                    $dueTodosResponseText .= 'Eine Aufgabe ist heute fällig';
                } else if ($dueTodayTodos->count() > 1) {
                    $dueTodosResponseText .= $dueTodayTodos->count() . ' Aufgaben sind heute fällig';
                }

                /* Check for todos due in the next five days */

                $dueTodosResponseText .= (!empty($dueTodosResponseText) ? ' und ' : '');
                $dueDays = 5;

                $dueNextDaysTodos = Todo::forEvent($this->currentEvent->id)->open()->dueInTheNextFewDays()->get();
                if ($dueNextDaysTodos->count() == 1) {
                    $dueTodosResponseText .= 'Eine Aufgabe ist in den nächsten ' . $dueDays . ' Tagen fällig.';
                } else if ($dueNextDaysTodos->count() > 1) {
                    $dueTodosResponseText .= $dueNextDaysTodos->count() . ' Aufgaben sind in den nächsten ' . $dueDays . ' Tagen fällig.';
                }

                $responseText = trim($responseText . ' ' . $dueTodosResponseText);
            }
        }

        return $responseText;
    }
}