<?php

namespace App\Intents;

class DefaultIntent extends Intent
{
    /* Methods */

    public function process()
    {
        return 'Es tut mir leid, das kann ich noch nicht.';
    }
}