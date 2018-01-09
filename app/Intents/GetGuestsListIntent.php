<?php

namespace App\Intents;

use App\Event;

class GetGuestsListIntent extends Intent
{
    /* Methods */

    public function process()
    {
        /* Fetch all guests */

        $guestsConfirmed = Guest::forEvent($this->currentEvent->id)->confirmed()->get();
        $guestsUndecided = Guest::forEvent($this->currentEvent->id)->undecided()->get();
        $guestsUnable = Guest::forEvent($this->currentEvent->id)->unable()->get();

        if ($guestsConfirmed->isEmpty() && $guestsUndecided->isEmpty() && $guestsUnable->isEmpty()) {
            $responseText = 'Es liegen noch keine Anmeldungen vor.';
        } else {
            $responseText = '';

            if ($guestsConfirmed->isEmpty()) {
                $responseText .= 'Es haben noch keine Gäste zugesagt';
            } else {
                if ($guestsConfirmed->count() == 1) {
                    $responseText .= 'Ein Gast hat zugesagt';
                } else {
                    $responseText .= $guestsConfirmed->count() . ' Gäste haben zugesagt';
                }
            }

            $responseText .= ', ';
            if ($guestsUnable->isEmpty()) {
                $responseText .= 'es haben noch keine Gäste abgesagt';
            } else {
                if ($guestsUnable->count() == 1) {
                    $responseText .= 'ein Gast hat abgesagt';
                } else {
                    $responseText .= $guestsUnable->count() . ' Gäste haben abgesagt';
                }
            }

            $responseText .= ' und ';
            if ($guestsUndecided->isEmpty()) {
                $responseText .= 'es sind keine Anmeldungen mehr offen';
            } else {
                if ($guestsUndecided->count() == 1) {
                    $responseText .= 'ein Gast hat sich noch nicht entschieden.';
                } else {
                    $responseText .= $guestsUndecided->count() . ' Gäste haben sich noch nicht entschieden.';
                }
            }
        }

        return $responseText;
    }
}