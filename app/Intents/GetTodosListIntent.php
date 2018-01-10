<?php

namespace App\Intents;

use App\Todo;

class GetTodosListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all guests */

        $todosCompleted = Todo::forEvent($this->currentEvent->id)->completed()->get();
        $todosOpen = Todo::forEvent($this->currentEvent->id)->open()->get();

        if ($todosCompleted->isEmpty() && $todosOpen->isEmpty()) {
            $responseText = 'Es liegen noch keine Aufgaben vor.';
        } else {
            $responseText = '';

            if ($todosCompleted->isEmpty()) {
                $responseText .= 'Es wurden noch keine Aufgaben erledigt';
            } else {
                if ($todosCompleted->count() == 1) {
                    $responseText .= 'Eine Aufgabe wurde erledigt';
                } else {
                    $responseText .= $todosCompleted->count() . ' Aufgaben wurden erledigt';
                }
            }

            $responseText .= ' und ';
            if ($todosOpen->isEmpty()) {
                $responseText .= 'es sind keine Aufgaben mehr offen.';
            } else {
                if ($todosOpen->count() == 1) {
                    $responseText .= 'eine Aufgabe ist noch offen.';
                } else {
                    $responseText .= $todosOpen->count() . ' Aufgaben sind noch offen.';
                }
            }
        }

        return $responseText;
    }
}