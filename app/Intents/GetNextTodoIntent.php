<?php

namespace App\Intents;

use App\Todo;

class GetNextTodoIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Overdue todos first */

        $nextOverdueTodo = Todo::forEvent($this->currentEvent->id)->open()->overdue()->oldest('due_at')->first();
        $nextDueTodo = Todo::forEvent($this->currentEvent->id)->open()->withDueDate()->oldest('due_at')->first();
        $nextUndueTodo = Todo::forEvent($this->currentEvent->id)->open()->withoutDueDate()->orderBy('id')->first();

        if (is_null($nextOverdueTodo) && is_null($nextDueTodo) && is_null($nextUndueTodo)) {
            return 'Es gibt zur Zeit keine offenen Aufgaben.';
        } else {
            if (!is_null($nextOverdueTodo)) {
                return $nextOverdueTodo->todo . ' ist bereits seit ' . $nextOverdueTodo->due_at->formatLocalized('%A den %d. %B %Y') . ' überfällig.';
            } else if (!is_null($nextDueTodo)) {
                return $nextDueTodo->todo . ' ist als nächstes am ' . $nextDueTodo->due_at->formatLocalized('%A den %d. %B %Y') . ' fällig.';
            } else if (!is_null($nextUndueTodo)) {
                return 'Die nächste offene Aufgabe ist ' . $nextUndueTodo->todo . '.';
            } else {
                return 'Es ist ein unbekannter Fehler bei der Bestimmung der nächsten Aufgabe aufgetreten.';
            }
        }
    }
}