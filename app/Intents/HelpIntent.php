<?php

namespace App\Intents;

use App\Event;

class HelpIntent extends Intent
{
    /* Methods */

    public function process()
    {
        return 'Mögliche Anweisungen lauten: Neue Veranstaltung erstellen, Veranstaltung wechseln, neuen Gast hinzufügen, Gast bestätigen, Gästeliste oder wer hat bereits zugesagt.';
    }
}